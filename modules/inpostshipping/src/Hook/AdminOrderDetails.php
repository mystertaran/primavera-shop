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

namespace InPost\Shipping\Hook;

use Carrier;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\DataProvider\OrderShipmentsDataProvider;
use InPost\Shipping\DataProvider\PointDataProvider;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\Presenter\PointAddressPresenter;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Views\Modal\CreateShipmentModal;
use InPost\Shipping\Views\Modal\MapModal;
use InPost\Shipping\Views\Modal\ShipmentDetailsModal;
use Order;

class AdminOrderDetails extends AbstractAdminOrdersHook
{
    const HOOK_LIST = [
        'displayAdminOrderTabShip',
        'displayAdminOrderContentShip',
    ];

    const HOOK_LIST_177 = [
        'displayAdminOrderTabLink',
        'displayAdminOrderTabContent',
    ];

    /** @var Order */
    protected $order;

    protected $templateVarsAssigned = false;

    public function hookDisplayAdminOrderTabShip($params)
    {
        $this->order = $params['order'];

        return $this->displayTabLink();
    }

    public function hookDisplayAdminOrderTabLink($params)
    {
        $this->order = new Order($params['id_order']);

        return $this->displayTabLink();
    }

    protected function displayTabLink()
    {
        if ($this->shouldDisplayOrderContent()) {
            $this->assignCommonTemplateVariables();

            return $this->module->display($this->module->name, $this->getTemplatePath('admin-order-tab-ship.tpl'));
        }

        return '';
    }

    public function hookDisplayAdminOrderContentShip($params)
    {
        $this->order = $params['order'];

        return $this->displayTabContent();
    }

    public function hookDisplayAdminOrderTabContent($params)
    {
        return $this->displayTabContent();
    }

    protected function displayTabContent()
    {
        if ($this->shouldDisplayOrderContent()) {
            $this->assignCommonTemplateVariables();

            return $this->module->display($this->module->name, $this->getTemplatePath('admin-order-content-ship.tpl'))
                . $this->renderModals();
        }

        return '';
    }

    protected function shouldDisplayOrderContent()
    {
        /** @var CustomerChoiceDataProvider $customerChoiceDataProvider */
        $customerChoiceDataProvider = $this->module->getService('inpost.shipping.data_provider.customer_choice');

        return !empty($customerChoiceDataProvider->getDataByCartId($this->order->id_cart))
            || (new Carrier($this->order->id_carrier))->external_module_name === $this->module->name;
    }

    protected function assignCommonTemplateVariables()
    {
        if (!$this->templateVarsAssigned) {
            /** @var OrderShipmentsDataProvider $shipmentsDataProvider */
            $shipmentsDataProvider = $this->module->getService('inpost.shipping.data_provider.order_shipments');
            $carrier = new Carrier($this->order->id_carrier);

            $this->context->smarty->assign([
                'inPostShipmentsListUrl' => $this->context->link->getAdminLink(Tabs::SHIPMENTS_CONTROLLER_NAME),
                'inPostShipments' => $shipmentsDataProvider->getOrderShipments($this->order->id),
                'inPostLockerAddress' => $this->getLockerAddress(),
                'carrierName' => $carrier->name,
            ]);

            $this->templateVarsAssigned = true;
        }
    }

    protected function renderModals()
    {
        return $this->renderDispatchOrderModal()
            . $this->renderShipmentFormModal()
            . $this->renderPrintShipmentLabelModal()
            . $this->renderShipmentDetailsModal()
            . $this->renderMapModal();
    }

    protected function renderShipmentFormModal()
    {
        /** @var CreateShipmentModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.shipment');

        return $modal
            ->setOrder($this->order)
            ->setTemplate($this->getTemplatePath('modal/create-shipment.tpl'))
            ->render();
    }

    protected function renderShipmentDetailsModal()
    {
        /** @var ShipmentDetailsModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.shipment_details');

        return $modal->render();
    }

    protected function renderMapModal()
    {
        /** @var MapModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.map');

        return $modal->render();
    }

    protected function getLockerAddress()
    {
        if (($pointName = $this->getPointNameByCartId($this->order->id_cart)) &&
            $point = $this->getLivePointData($pointName)
        ) {
            /** @var PointAddressPresenter $pointAddressPresenter */
            $pointAddressPresenter = $this->module->getService('inpost.shipping.presenter.point_address');

            return $pointAddressPresenter->present($point, false, $this->context->language->id);
        }

        return null;
    }

    protected function getLivePointData($pointName)
    {
        /** @var PointDataProvider $pointDataProvider */
        $pointDataProvider = $this->module->getService('inpost.shipping.data_provider.point');
        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');

        $configuration->setSandboxMode(false);
        $pointData = $pointDataProvider->getPointData($pointName);
        $configuration->setSandboxMode(null);

        return $pointData;
    }

    protected function getPointNameByCartId($id_cart)
    {
        /** @var CustomerChoiceDataProvider $customerChoiceDataProvider */
        $customerChoiceDataProvider = $this->module->getService('inpost.shipping.data_provider.customer_choice');

        if (($customerChoice = $customerChoiceDataProvider->getDataByCartId($id_cart)) &&
            $customerChoice->service === Service::INPOST_LOCKER_STANDARD
        ) {
            return $customerChoice->point;
        }

        return null;
    }
}
