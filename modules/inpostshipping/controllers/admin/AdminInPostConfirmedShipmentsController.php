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

use InPost\Shipping\Handler\DispatchOrder\CreateDispatchOrderHandler;
use InPost\Shipping\Handler\Shipment\BulkCreateShipmentHandler;
use InPost\Shipping\Handler\Shipment\CreateShipmentHandler;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\Presenter\ShipmentPresenter;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\ShipX\Resource\Status;
use InPost\Shipping\Views\Modal\ShipmentDetailsModal;

require_once dirname(__FILE__) . '/AdminInPostShipmentsController.php';

class AdminInPostConfirmedShipmentsController extends AdminInPostShipmentsController
{
    const TRANSLATION_SOURCE = 'AdminInPostConfirmedShipmentsController';

    public function __construct()
    {
        parent::__construct();

        $this->_select .= ' , IF(
            a.sending_method = "' . SendingMethod::DISPATCH_ORDER . '",
            a.id_dispatch_order IS NOT NULL,
            NULL
        ) as dispatch_order';
        $this->_where .= ' AND a.status LIKE "' . Status::STATUS_CONFIRMED . '"';
    }

    protected function getFieldsList()
    {
        return array_merge(parent::getFieldsList(), [
            'label_printed' => [
                'type' => 'bool',
                'title' => $this->module->l('Label printed', self::TRANSLATION_SOURCE),
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'callback' => 'displayBoolean',
            ],
            'dispatch_order' => [
                'type' => 'bool',
                'title' => $this->module->l('Dispatch order', self::TRANSLATION_SOURCE),
                'tmpTableFilter' => true,
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'callback' => 'displayBoolean',
            ],
        ]);
    }

    protected function addListActions()
    {
        parent::addListActions();

        $this->addRowAction('createDispatchOrder');
        $this->addRowActionSkipList(
            'createDispatchOrder',
            InPostShipmentModel::getSkipCreateDispatchOrderList($this->organizationId, $this->sandbox)
        );

        $this->bulk_actions = array_merge(
            array_slice($this->bulk_actions, 0, 2),
            [
                'createDispatchOrders' => [
                    'text' => $this->module->l('Create dispatch orders', self::TRANSLATION_SOURCE),
                    'icon' => 'icon-truck',
                ],
            ],
            array_slice($this->bulk_actions, 2)
        );
    }

    protected function getModalServices()
    {
        return array_merge(parent::getModalServices(), [
            'inpost.shipping.views.modal.dispatch_order',
        ]);
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        parent::initBreadcrumbs($tab_id, $tabs);

        if (!$this->shopContext->is17()) {
            $this->breadcrumbs[] = $this->module->l('Confirmed shipments', Tabs::TRANSLATION_SOURCE);
        }
    }

    public function ajaxProcessCreateShipment()
    {
        /** @var CreateShipmentHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.shipment.create');

        if ($shipment = $handler->handle(Tools::getAllValues())) {
            $presenter = $this->module->getService(ShipmentPresenter::class);
            $this->response['shipment'] = $presenter->present($shipment);
        }

        $this->errors = $handler->getErrors();
    }

    public function ajaxProcessBulkCreateShipment()
    {
        /** @var BulkCreateShipmentHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.shipment.bulk_create');

        if ($shipments = $handler->handle(Tools::getAllValues())) {
            $this->response['shipmentIds'] = array_map(static function (InPostShipmentModel $shipment) {
                return $shipment->id;
            }, $shipments);
        }

        if ($handler->hasErrors()) {
            $this->errors = $handler->getErrors();
        } else {
            $this->response['redirect'] = $this->link->getAdminLink($this->controller_name);
        }
    }

    public function ajaxProcessViewShipment()
    {
        /** @var InPostShipmentModel $shipment */
        if (!$shipment = $this->loadObject()) {
            return;
        }

        /** @var ShipmentDetailsModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.shipment_details');

        $template = $this->shopContext->is177()
            ? 'views/templates/hook/177/modal/shipment-details.tpl'
            : 'views/templates/hook/modal/shipment-details.tpl';

        $this->response['content'] = $modal->setShipment($shipment)
            ->setTemplate($template)
            ->renderContent();
    }

    public function ajaxProcessCreateDispatchOrder()
    {
        /** @var InPostShipmentModel $shipment */
        if (!$shipment = $this->loadObject()) {
            return;
        }

        if ($shipment->id_dispatch_order) {
            $this->errors[] = $this->module->l('A dispatch order for this shipment already exists', self::TRANSLATION_SOURCE);

            return;
        }

        /** @var CreateDispatchOrderHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.dispatch_order.create');

        if ($result = $handler->handle([$shipment->shipx_shipment_id], Tools::getValue('id_dispatch_point'))) {
            $shipment->id_dispatch_order = $result->id;
            $shipment->update();

            $this->dispatchOrderRedirect();
        } else {
            $this->errors = $handler->getErrors();
        }
    }

    public function ajaxProcessBulkCreateDispatchOrders()
    {
        if (Tools::isSubmit('orderIds')) {
            if (!$this->boxes = $this->getOrderShipments()) {
                return;
            }
        } else {
            $this->boxes = Tools::getValue($this->table . 'Box');
        }

        $this->processBulkCreateDispatchOrders();
    }

    public function processBulkCreateDispatchOrders()
    {
        if (!is_array($this->boxes) || empty($this->boxes)) {
            $this->errors[] = $this->module->l('You must select at least one item', self::TRANSLATION_SOURCE);

            return;
        }

        $skipList = $this->list_skip_actions['createdispatchorder'];
        $boxes = array_filter($this->boxes, static function ($id) use ($skipList) {
            return !isset($skipList[$id]);
        });

        if (empty($boxes)) {
            $this->errors[] = $this->module->l('All of the selected shipments already have dispatch orders or have a different sending method', self::TRANSLATION_SOURCE);

            return;
        }

        /** @var CreateDispatchOrderHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.dispatch_order.create');

        $collection = (new PrestaShopCollection(InPostShipmentModel::class))
            ->where('id_shipment', '=', $boxes)
            ->where('sending_method', '=', SendingMethod::DISPATCH_ORDER);

        $shipmentIndex = [];
        $idIndex = [];

        /** @var InPostShipmentModel $shipment */
        foreach ($collection as $shipment) {
            $key = in_array($shipment->service, Service::LOCKER_CARRIER_SERVICES, true) ? 'locker' : 'courier';
            $shipmentIndex[$key][] = $shipment;
            $idIndex[$key][] = $shipment->shipx_shipment_id;
        }

        foreach ($idIndex as $key => $ids) {
            if ($result = $handler->handle($ids, Tools::getValue('id_dispatch_point'))) {
                foreach ($shipmentIndex[$key] as $shipment) {
                    $shipment->id_dispatch_order = $result->id;
                    $shipment->update();
                }
            } else {
                $this->errors = $handler->getErrors();
            }
        }

        $this->dispatchOrderRedirect();
    }

    protected function dispatchOrderRedirect()
    {
        $url = $this->link->getAdminLink(Tabs::DISPATCH_ORDERS_CONTROLLER_NAME, true, [], [
            'conf' => 4,
        ]);

        if ($this->ajax) {
            $this->response['redirect'] = $url;
        } else {
            $this->redirect_after = $url;
        }
    }

    public function displayBoolean($value)
    {
        if (null === $value) {
            return null;
        }

        return $value
            ? $this->module->l('Yes', self::TRANSLATION_SOURCE)
            : $this->module->l('No', self::TRANSLATION_SOURCE);
    }
}
