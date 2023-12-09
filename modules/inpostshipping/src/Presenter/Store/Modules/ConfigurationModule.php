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

namespace InPost\Shipping\Presenter\Store\Modules;

use InPost\Shipping\ChoiceProvider\ModuleChoiceProvider;
use InPost\Shipping\ChoiceProvider\ModulePageChoiceProvider;
use InPost\Shipping\ChoiceProvider\OrderStateChoiceProvider;
use InPost\Shipping\ChoiceProvider\ShipmentReferenceFieldChoiceProvider;
use InPost\Shipping\Configuration\CheckoutConfiguration;
use InPost\Shipping\Configuration\GeoWidgetConfiguration;
use InPost\Shipping\Configuration\OrdersConfiguration;
use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Configuration\SzybkieZwrotyConfiguration;
use InPost\Shipping\Presenter\Store\PresenterInterface;

class ConfigurationModule implements PresenterInterface
{
    protected $shipXConfiguration;
    protected $geoWidgetConfiguration;
    protected $sendingConfiguration;
    protected $ordersConfiguration;
    protected $checkoutConfiguration;
    protected $szybkieZwrotyConfiguration;
    protected $referenceFieldChoiceProvider;
    protected $orderStateChoiceProvider;
    protected $moduleChoiceProvider;
    protected $modulePageChoiceProvider;

    public function __construct(
        ShipXConfiguration $shipXConfiguration,
        GeoWidgetConfiguration $geoWidgetConfiguration,
        SendingConfiguration $sendingConfiguration,
        OrdersConfiguration $ordersConfiguration,
        CheckoutConfiguration $checkoutConfiguration,
        SzybkieZwrotyConfiguration $szybkieZwrotyConfiguration,
        ShipmentReferenceFieldChoiceProvider $referenceFieldChoiceProvider,
        OrderStateChoiceProvider $orderStateChoiceProvider,
        ModuleChoiceProvider $moduleChoiceProvider,
        ModulePageChoiceProvider $modulePageChoiceProvider
    ) {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->geoWidgetConfiguration = $geoWidgetConfiguration;
        $this->sendingConfiguration = $sendingConfiguration;
        $this->ordersConfiguration = $ordersConfiguration;
        $this->checkoutConfiguration = $checkoutConfiguration;
        $this->szybkieZwrotyConfiguration = $szybkieZwrotyConfiguration;
        $this->referenceFieldChoiceProvider = $referenceFieldChoiceProvider;
        $this->orderStateChoiceProvider = $orderStateChoiceProvider;
        $this->moduleChoiceProvider = $moduleChoiceProvider;
        $this->modulePageChoiceProvider = $modulePageChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        $modulePageChoices = $this->modulePageChoiceProvider->getChoices();

        return [
            'config' => [
                'api' => [
                    'token' => $this->shipXConfiguration->getProductionApiToken(),
                    'organizationId' => $this->shipXConfiguration->getProductionOrganizationId() ?: '',
                    'sandbox' => [
                        'enabled' => $this->shipXConfiguration->isSandboxModeEnabled(),
                        'token' => $this->shipXConfiguration->getSandboxApiToken(),
                        'organizationId' => $this->shipXConfiguration->getSandboxOrganizationId() ?: '',
                    ],
                ],
                'geoWidget' => [
                    'token' => $this->geoWidgetConfiguration->getProductionToken(),
                    'sandbox' => [
                        'token' => $this->geoWidgetConfiguration->getSandboxToken(),
                        'useOnTheFrontOffice' => $this->geoWidgetConfiguration->shouldUseSandboxOnTheFrontOffice(),
                    ],
                ],
                'sending' => [
                    'reload' => false,
                    'sender' => $this->sendingConfiguration->getSenderDetails(),
                    'defaults' => [
                        'sendingMethod' => $this->sendingConfiguration->getDefaultSendingMethod(),
                        'locker' => $this->sendingConfiguration->getDefaultLocker(),
                        'pop' => $this->sendingConfiguration->getDefaultPOP(),
                        'dispatchPoint' => $this->sendingConfiguration->getDefaultDispatchPointId(),
                        'referenceField' => $this->sendingConfiguration->getDefaultShipmentReferenceField(),
                        'insuranceAmount' => $this->sendingConfiguration->getDefaultInsuranceAmount(),
                    ],
                    'referenceFieldChoices' => $this->referenceFieldChoiceProvider->getChoices(),
                ],
                'orders' => [
                    'mails' => [
                        'displayLocker' => $this->ordersConfiguration->shouldDisplayOrderConfirmationLocker(),
                    ],
                    'labelPrinted' => [
                        'changeOrderStatus' => $this->ordersConfiguration->shouldChangeOrderStateOnShipmentLabelPrinted(),
                        'orderStateId' => $this->ordersConfiguration->getShipmentLabelPrintedOrderStateId(),
                    ],
                    'shipmentDelivered' => [
                        'changeOrderStatus' => $this->ordersConfiguration->shouldChangeOrderStateOnShipmentDelivered(),
                        'orderStateId' => $this->ordersConfiguration->getShipmentDeliveredOrderStateId(),
                    ],
                    'orderStateChoices' => $this->orderStateChoiceProvider->getChoices(),
                ],
                'checkout' => [
                    'usingCustomModule' => $this->checkoutConfiguration->isUsingCustomCheckoutModule(),
                    'showInputEmail' => $this->checkoutConfiguration->getShowInputEmail(),
                    'showInputPhone' => $this->checkoutConfiguration->getShowInputPhone(),
                    'customControllers' => $this->checkoutConfiguration->getCustomCheckoutControllers(),
                    'moduleChoices' => $this->filterModuleChoices($modulePageChoices),
                    'modulePageChoices' => $modulePageChoices,
                ],
                'szybkieZwroty' => [
                    'storeName' => $this->szybkieZwrotyConfiguration->getStoreName(),
                    'urlTemplate' => $this->szybkieZwrotyConfiguration->getUrlTemplate(),
                ],
            ],
        ];
    }

    protected function filterModuleChoices(array $pageChoices)
    {
        return array_filter(
            $this->moduleChoiceProvider->getChoices(),
            static function (array $choice) use ($pageChoices) {
                return isset($pageChoices[$choice['value']]);
            }
        );
    }
}
