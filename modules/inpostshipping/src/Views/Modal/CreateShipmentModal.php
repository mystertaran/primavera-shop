<?php
/**
 * Copyright 2021-2023 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2023 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */

namespace InPost\Shipping\Views\Modal;

use Address;
use Currency;
use Customer;
use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\ChoiceProvider\DimensionTemplateChoiceProvider;
use InPost\Shipping\ChoiceProvider\SendingMethodChoiceProvider;
use InPost\Shipping\ChoiceProvider\ShippingServiceChoiceProvider;
use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\DataProvider\CarrierDataProvider;
use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\DataProvider\OrderDimensionsDataProvider;
use InPost\Shipping\Helper\DefaultShipmentReferenceExtractor;
use InPost\Shipping\Helper\ParcelDimensionsComparator;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\PrestaShopContext;
use InPost\Shipping\ShipX\Resource\Service;
use InPostShipping;
use Order;
use Tools;

class CreateShipmentModal extends AbstractModal
{
    const TRANSLATION_SOURCE = 'CreateShipmentModal';

    const MODAL_ID = 'inpost-create-shipment-modal';

    protected $customerChoiceDataProvider;
    protected $shippingServiceChoiceProvider;
    protected $sendingMethodChoiceProvider;
    protected $dimensionTemplateChoiceProvider;
    protected $sendingConfiguration;
    protected $carriersConfiguration;
    protected $carrierDataProvider;
    protected $referenceExtractor;
    protected $orderDimensionsDataProvider;
    protected $dimensionsComparator;

    /** @var Order */
    protected $order;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        PrestaShopContext $shopContext,
        CustomerChoiceDataProvider $customerChoiceDataProvider,
        ShippingServiceChoiceProvider $shippingServiceChoiceProvider,
        SendingMethodChoiceProvider $sendingMethodChoiceProvider,
        DimensionTemplateChoiceProvider $dimensionTemplateChoiceProvider,
        SendingConfiguration $sendingConfiguration,
        CarriersConfiguration $carriersConfiguration,
        CarrierDataProvider $carrierDataProvider,
        DefaultShipmentReferenceExtractor $referenceExtractor,
        OrderDimensionsDataProvider $orderDimensionsDataProvider,
        ParcelDimensionsComparator $dimensionsComparator
    ) {
        parent::__construct($module, $link, $shopContext);

        $this->customerChoiceDataProvider = $customerChoiceDataProvider;
        $this->shippingServiceChoiceProvider = $shippingServiceChoiceProvider;
        $this->sendingMethodChoiceProvider = $sendingMethodChoiceProvider;
        $this->dimensionTemplateChoiceProvider = $dimensionTemplateChoiceProvider;
        $this->sendingConfiguration = $sendingConfiguration;
        $this->carriersConfiguration = $carriersConfiguration;
        $this->carrierDataProvider = $carrierDataProvider;
        $this->referenceExtractor = $referenceExtractor;
        $this->orderDimensionsDataProvider = $orderDimensionsDataProvider;
        $this->dimensionsComparator = $dimensionsComparator;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;

        return $this;
    }

    protected function assignContentTemplateVariables()
    {
        if (!isset($this->order)) {
            return;
        }

        $customerChoice = $this->customerChoiceDataProvider->getDataByCartId($this->order->id_cart);
        $inPostCarrier = $this->carrierDataProvider->getInPostCarrierByCarrierId($this->order->id_carrier);
        $service = $customerChoice
            ? $customerChoice->service
            : $inPostCarrier->service;
        $defaultSendingMethods = $this->carriersConfiguration->getDefaultSendingMethods();
        $useProductDimensions = null !== $inPostCarrier && $inPostCarrier->use_product_dimensions;
        $dimensions = $this->getDimensions($service, $useProductDimensions);
        $cashOnDelivery = null !== $inPostCarrier ? $inPostCarrier->cod : false;
        $orderTotal = Tools::math_round($this->order->total_paid, 2);
        $orderCurrency = Currency::getCurrencyInstance($this->order->id_currency);

        if ($insuranceAmount = $this->sendingConfiguration->getDefaultInsuranceAmount()) {
            $insuranceAmount = Tools::convertPriceFull(
                $insuranceAmount,
                Currency::getDefaultCurrency(),
                $orderCurrency
            );

            if (
                $cashOnDelivery &&
                $insuranceAmount < $orderTotal &&
                in_array($service, Service::LOCKER_SERVICES, true)
            ) {
                $insuranceAmount = $orderTotal;
            }
        }

        if ($customerChoice) {
            $templateVariables = [
                'customerEmail' => $customerChoice->email,
                'customerPhone' => $customerChoice->phone,
                'selectedPoint' => $customerChoice->point,
            ];
        } else {
            $address = new Address($this->order->id_address_delivery);
            $templateVariables = [
                'customerEmail' => (new Customer($this->order->id_customer))->email,
                'customerPhone' => $address->phone_mobile ?: $address->phone,
                'selectedPoint' => '',
            ];
        }

        if (!$weight = $this->order->getTotalWeight()) {
            $weight = isset($dimensions['weight']) ? $dimensions['weight'] : 0;
        }

        $this->context->smarty->assign(array_merge($templateVariables, [
            'shipmentAction' => $this->link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                'action' => 'createShipment',
            ]),
            'id_order' => $this->order->id,
            'serviceChoices' => $this->shippingServiceChoiceProvider->getChoices(),
            'selectedService' => $service,
            'commercialProductIdentifier' => null !== $inPostCarrier ? $inPostCarrier->commercial_product_identifier : null,
            'sendingMethodChoices' => $this->sendingMethodChoiceProvider->getChoices(),
            'defaultSendingMethods' => $defaultSendingMethods,
            'defaultSendingMethod' => isset($defaultSendingMethods[$service])
                ? $defaultSendingMethods[$service]
                : $this->sendingConfiguration->getDefaultSendingMethod(),
            'defaultPop' => $this->sendingConfiguration->getDefaultPOP(),
            'defaultLocker' => $this->sendingConfiguration->getDefaultLocker(),
            'dimensionTemplateChoices' => $this->dimensionTemplateChoiceProvider->getChoices(),
            'useTemplate' => $this->shouldUseDimensionsTemplate($service, $dimensions),
            'template' => $this->getTemplate($service, $useProductDimensions),
            'shipmentReference' => $this->referenceExtractor->getShipmentReference($this->order),
            'length' => $dimensions ? $dimensions['length'] : 0,
            'width' => $dimensions ? $dimensions['width'] : 0,
            'height' => $dimensions ? $dimensions['height'] : 0,
            'weight' => $weight,
            'cashOnDelivery' => $cashOnDelivery,
            'weekendDelivery' => null !== $inPostCarrier ? $inPostCarrier->weekend_delivery : false,
            'orderTotal' => Tools::math_round($this->order->total_paid, 2),
            'insurance' => null !== $insuranceAmount,
            'insuranceAmount' => $insuranceAmount ?: $orderTotal,
            'currencySign' => $orderCurrency->sign,
            'defaultTemplates' => $this->carriersConfiguration->getDefaultDimensionTemplates(),
        ]));
    }

    protected function getDimensions($service, $useProductDimensions)
    {
        $defaultDimensions = $this->carriersConfiguration->getDefaultShipmentDimensions($service);
        if ($useProductDimensions) {
            $orderDimensions = $this->orderDimensionsDataProvider->getLargestProductDimensionsByOrderId($this->order->id);

            if (null !== $orderDimensions) {
                return null !== $defaultDimensions
                    ? array_merge($defaultDimensions, $orderDimensions)
                    : $orderDimensions;
            }
        }

        return $defaultDimensions;
    }

    protected function getTemplate($service, $useProductDimensions)
    {
        if (
            $useProductDimensions &&
            in_array($service, Service::LOCKER_CARRIER_SERVICES, true) &&
            $templates = $this->orderDimensionsDataProvider->getProductDimensionTemplatesByOrderId($this->order->id)
        ) {
            return $this->dimensionsComparator->getLargestTemplate($templates);
        }

        return $this->carriersConfiguration->getDefaultDimensionTemplates($service);
    }

    public function renderContent()
    {
        return isset($this->order) ? parent::renderContent() : '';
    }

    protected function getTitle()
    {
        return $this->module->l('Create shipment', self::TRANSLATION_SOURCE);
    }

    protected function getClasses()
    {
        return $this->shopContext->is177() ? '' : 'modal-lg';
    }

    protected function getActions()
    {
        return [
            [
                'type' => 'button',
                'value' => 'submitShipment',
                'class' => 'js-submit-shipment-form js-submit-shipment-form-btn btn-primary',
                'label' => $this->module->l('Submit', self::TRANSLATION_SOURCE),
            ],
            [
                'type' => 'link',
                'href' => $this->link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME, true, [], [
                    'ajax' => true,
                    'action' => 'printLabel',
                ]),
                'class' => 'js-submit-shipment-form-and-print-label js-submit-shipment-form-btn btn-primary',
                'label' => $this->module->l('Submit and print label', self::TRANSLATION_SOURCE),
            ],
        ];
    }

    protected function shouldUseDimensionsTemplate($service, $dimensions)
    {
        return in_array($service, Service::LOCKER_CARRIER_SERVICES, true) && !$dimensions;
    }
}
