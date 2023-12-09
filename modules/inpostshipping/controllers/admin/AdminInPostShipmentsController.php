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
require_once dirname(__FILE__) . '/InPostShippingAdminController.php';

use InPost\Shipping\CarrierUpdater;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Configuration\SzybkieZwrotyConfiguration;
use InPost\Shipping\Handler\Shipment\PrintShipmentLabelHandler;
use InPost\Shipping\Handler\Shipment\UpdateShipmentStatusHandler;
use InPost\Shipping\ShipX\Exception\LabelNotFoundException;
use InPost\Shipping\ShipX\Resource\Organization\DispatchOrder;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Translations\SendingMethodTranslator;
use InPost\Shipping\Translations\ShippingServiceTranslator;
use InPost\Shipping\Views\Modal\AbstractModal;
use InPost\Shipping\Views\ShipmentNavTabs;

abstract class AdminInPostShipmentsController extends InPostShippingAdminController
{
    const TRANSLATION_SOURCE = 'AdminInPostShipmentsController';

    /** @var InPostShipmentModel */
    protected $object;

    protected $sandbox;
    protected $organizationId;

    protected $sendingMethodList;
    protected $shippingServiceList;

    protected $szybkieZwrotyUrl;
    protected $parcelLockerShipmentIndex;

    public function __construct()
    {
        $this->table = 'inpost_shipment';
        $this->identifier = 'id_shipment';
        $this->bootstrap = true;
        $this->list_no_link = true;

        parent::__construct();

        $this->className = InPostShipmentModel::class;

        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');
        $this->sandbox = $configuration->isSandboxModeEnabled();
        $this->organizationId = $configuration->getOrganizationId();

        if ($this->sandbox) {
            $this->warnings[] = $this->module->l('Sandbox mode is enabled', self::TRANSLATION_SOURCE);
        }

        $this->_select = 'o.reference as order_reference, o.id_currency';
        $this->_join = 'INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = a.id_order';
        $this->_where = '
            AND a.sandbox = ' . ($this->sandbox ? 1 : 0) . '
            AND a.organization_id = ' . $this->organizationId;
        $this->_orderWay = 'desc';

        $this->fields_list = $this->getFieldsList();

        if ($configuration->hasConfiguration()) {
            $this->addListActions();
        }
    }

    protected function getFieldsList()
    {
        return [
            'tracking_number' => [
                'title' => $this->module->l('Shipment number', self::TRANSLATION_SOURCE),
                'callback' => 'displayTrackingUrl',
            ],
            'order_reference' => [
                'title' => $this->module->l('Order reference', self::TRANSLATION_SOURCE),
                'callback' => 'displayOrderReference',
                'filter_key' => 'o!reference',
            ],
            'price' => [
                'type' => 'price',
                'currency' => true,
                'title' => $this->module->l('Price', self::TRANSLATION_SOURCE),
                'search' => false,
                'class' => 'fixed-width-xs',
            ],
            'service' => [
                'title' => $this->module->l('Service', self::TRANSLATION_SOURCE),
                'type' => 'select',
                'list' => $this->getShippingServiceList(),
                'filter_key' => 'a!service',
                'callback' => 'displayShippingServiceName',
            ],
            'sending_method' => [
                'title' => $this->module->l('Sending method', self::TRANSLATION_SOURCE),
                'type' => 'select',
                'list' => $this->getSendingMethodList(),
                'filter_key' => 'a!sending_method',
                'callback' => 'displaySendingMethodName',
            ],
            'phone' => [
                'title' => $this->module->l('Receiver phone', self::TRANSLATION_SOURCE),
            ],
            'email' => [
                'title' => $this->module->l('Receiver email', self::TRANSLATION_SOURCE),
            ],
            'reference' => [
                'title' => $this->module->l('Reference', self::TRANSLATION_SOURCE),
                'filter_key' => 'a!reference',
            ],
            'date_add' => [
                'title' => $this->module->l('Created at', self::TRANSLATION_SOURCE),
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ],
        ];
    }

    protected function addListActions()
    {
        $this->addRowAction('printLabel');
        $this->addRowAction('printReturnLabel');
        $this->addRowAction('printDispatchOrder');

        $this->addRowActionSkipList(
            'printDispatchOrder',
            InPostShipmentModel::getSkipPrintDispatchOrderList($this->organizationId, $this->sandbox)
        );

        $this->bulk_actions = [
            'printLabels' => [
                'text' => $this->module->l('Print labels', self::TRANSLATION_SOURCE),
                'icon' => 'icon-print',
            ],
            'printReturnLabels' => [
                'text' => $this->module->l('Print return labels', self::TRANSLATION_SOURCE),
                'icon' => 'icon-print',
            ],
            'printDispatchOrders' => [
                'text' => $this->module->l('Print dispatch orders', self::TRANSLATION_SOURCE),
                'icon' => 'icon-print',
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->module->getAssetsManager()
            ->registerJavaScripts([
                'admin/tools.js',
                'admin/common.js',
                'admin/shipments.js',
            ])
            ->registerStyleSheets([
                'admin/table-fix.css',
            ]);

        Media::addJsDef([
            'controllerUrl' => $this->link->getAdminLink($this->controller_name),
        ]);

        if (!$this->shopContext->is17()) {
            $this->module->getAssetsManager()
                ->registerStyleSheets([
                    'admin/nav-tabs.css',
                ]);
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();

        unset($this->toolbar_btn['new']);

        $this->toolbar_btn['status_refresh'] = [
            'href' => $this->link->getAdminLink($this->controller_name, true, [], [
                'action' => 'refreshStatuses',
            ]),
            'desc' => $this->module->l('Refresh shipment statuses', self::TRANSLATION_SOURCE),
            'imgclass' => 'refresh',
        ];
    }

    public function initModal()
    {
        parent::initModal();

        foreach ($this->getModalServices() as $service) {
            /** @var AbstractModal $modal */
            $modal = $this->module->getService($service);
            $this->modals[] = $modal->getModalData();
        }
    }

    protected function getModalServices()
    {
        return [
            'inpost.shipping.views.modal.print_label',
        ];
    }

    public function ajaxProcessBulkRefreshStatuses()
    {
        if ($shipmentsIds = $this->getOrderShipments()) {
            $this->processRefreshStatuses($shipmentsIds);
        }
    }

    public function processRefreshStatuses(array $shipmentIds = [])
    {
        /** @var UpdateShipmentStatusHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.shipment.update_status');

        $handler->handle($shipmentIds);

        if ($this->ajax) {
            $this->response['message'] = $this->module->l('Shipment statuses have been updated', self::TRANSLATION_SOURCE);
        } else {
            $this->redirect_after = $this->link->getAdminLink($this->controller_name, true, [], [
                'conf' => 4,
            ]);
        }
    }

    public function ajaxProcessPrintLabel()
    {
        $this->processPrintLabel();
    }

    public function processPrintLabel()
    {
        /** @var InPostShipmentModel $shipmentModel */
        if ($shipmentModel = $this->loadObject()) {
            /** @var PrintShipmentLabelHandler $handler */
            $handler = $this->module->getService('inpost.shipping.handler.shipment.print_label');

            $label = $handler->handle(
                $shipmentModel,
                $this->resolvePrintOptions(true)
            );

            $this->offerDownload($label, $shipmentModel->service);
        }
    }

    public function ajaxProcessBulkPrintLabels()
    {
        $this->boxes = Tools::getValue($this->table . 'Box');
        $this->processBulkPrintLabels();
    }

    public function processBulkPrintLabels()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            /** @var PrintShipmentLabelHandler $handler */
            $handler = $this->module->getService('inpost.shipping.handler.shipment.print_label');

            $shipmentModels = InPostShipmentModel::getByIds(
                $this->boxes,
                $this->sandbox,
                $this->organizationId
            );

            $labels = $handler->handleMultiple(
                $shipmentModels,
                $this->resolvePrintOptions(true)
            );

            $this->offerDownload($labels);
        } else {
            $this->errors[] = $this->module->l('You must select at least one item', self::TRANSLATION_SOURCE);
        }
    }

    public function ajaxProcessPrintReturnLabel()
    {
        $this->processPrintReturnLabel();
    }

    public function processPrintReturnLabel()
    {
        /** @var InPostShipmentModel $shipmentModel */
        if (!$shipmentModel = $this->loadObject()) {
            return;
        }

        if (in_array($shipmentModel->service, Service::LOCKER_CARRIER_SERVICES, true)) {
            $url = $this->getSzybkieZwrotyUrl();
            if ($this->ajax) {
                $this->response['redirect'] = $url;
            } else {
                Tools::redirect($url);
            }
        } else {
            $this->offerDownload(Shipment::getReturnLabel(
                $shipmentModel->shipx_shipment_id,
                $this->resolvePrintOptions()
            ));
        }
    }

    public function ajaxProcessBulkPrintReturnLabels()
    {
        $this->boxes = Tools::getValue($this->table . 'Box');
        $this->processBulkPrintReturnLabels();
    }

    public function processBulkPrintReturnLabels()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $shipmentIds = InPostShipmentModel::getShipXShipmentIds($this->boxes, $this->sandbox, true);

            $this->offerDownload(Shipment::getMultipleReturnLabels(
                $shipmentIds,
                $this->resolvePrintOptions()
            ));
        } else {
            $this->errors[] = $this->module->l('You must select at least one item', self::TRANSLATION_SOURCE);
        }
    }

    protected function resolvePrintOptions($withType = false)
    {
        $query = [];

        if ($withType) {
            $query['type'] = in_array($type = Tools::getValue('label_type'), Shipment::LABEL_TYPES)
                ? $type
                : Shipment::TYPE_A6;
        }

        $query['format'] = in_array($format = Tools::getValue('label_format'), Shipment::LABEL_FORMATS)
            ? $format
            : Shipment::FORMAT_PDF;

        return [
            'query' => $query,
        ];
    }

    public function ajaxProcessPrintDispatchOrder()
    {
        $this->processPrintDispatchOrder();
    }

    public function processPrintDispatchOrder()
    {
        /** @var InPostShipmentModel $shipmentModel */
        if ($shipmentModel = $this->loadObject()) {
            if ($shipmentModel->id_dispatch_order) {
                $this->offerDownload(
                    DispatchOrder::getPrintoutsByShipmentIds([$shipmentModel->shipx_shipment_id]),
                    'dispatch_order'
                );
            } else {
                $this->errors[] = $this->module->l('No dispatch order for the selected shipment', self::TRANSLATION_SOURCE);
            }
        }
    }

    public function ajaxProcessBulkPrintDispatchOrders()
    {
        if (Tools::isSubmit('orderIds')) {
            if (!$this->boxes = $this->getOrderShipments()) {
                return;
            }
        } else {
            $this->boxes = Tools::getValue($this->table . 'Box');
        }

        $this->processBulkPrintDispatchOrders();
    }

    public function processBulkPrintDispatchOrders()
    {
        if (is_array($this->boxes) && !empty($this->boxes)) {
            $skipList = $this->list_skip_actions['printdispatchorder'];
            $boxes = array_filter($this->boxes, function ($id) use ($skipList) {
                return !isset($skipList[$id]);
            });

            if (!empty($boxes)) {
                $ids = InPostShipmentModel::getShipXShipmentIds($this->boxes, $this->sandbox, false, true);

                $this->offerDownload(DispatchOrder::getPrintoutsByShipmentIds($ids));
            } else {
                $this->errors[] = $this->module->l('None of the selected shipments has dispatch orders', self::TRANSLATION_SOURCE);
            }
        } else {
            $this->errors[] = $this->module->l('You must select at least one item', self::TRANSLATION_SOURCE);
        }
    }

    protected function getOrderShipments()
    {
        if ($orderIds = Tools::getValue('orderIds', [])) {
            $shipmentIds = InPostShipmentModel::getShipmentIdsByOrderIds(
                $orderIds,
                $this->sandbox,
                $this->organizationId
            );

            if (empty($shipmentIds)) {
                $this->errors[] = $this->module->l('There exist no InPost shipments for selected orders', self::TRANSLATION_SOURCE);
            }

            return $shipmentIds;
        } else {
            $this->errors[] = $this->module->l('You must select at least one item', self::TRANSLATION_SOURCE);
        }

        return [];
    }

    protected function getSendingMethodList()
    {
        if (!isset($this->sendingMethodList)) {
            $this->sendingMethodList = [];

            /** @var SendingMethodTranslator $translator */
            $translator = $this->module->getService('inpost.shipping.translations.sending_method');
            foreach (SendingMethod::SENDING_METHODS as $method) {
                $this->sendingMethodList[$method] = $translator->translate($method);
            }
        }

        return $this->sendingMethodList;
    }

    protected function getShippingServiceList()
    {
        if (!isset($this->shippingServiceList)) {
            $this->shippingServiceList = [];

            /** @var ShippingServiceTranslator $translator */
            $translator = $this->module->getService('inpost.shipping.translations.shipping_service');
            foreach (Service::SERVICES as $service) {
                $this->shippingServiceList[$service] = $translator->translate($service);
            }
        }

        return $this->shippingServiceList;
    }

    protected function getSzybkieZwrotyUrl()
    {
        if (!isset($this->szybkieZwrotyUrl)) {
            /** @var SzybkieZwrotyConfiguration $configuration */
            $configuration = $this->module->getService('inpost.shipping.configuration.szybkie_zwroty');
            $this->szybkieZwrotyUrl = $configuration->getOrderReturnFormUrl(true);
        }

        return $this->szybkieZwrotyUrl;
    }

    public function displayShippingServiceName($service, array $shipment)
    {
        $list = $this->getShippingServiceList();

        $name = isset($list[$service]) ? $list[$service] : $service;
        if (Service::INPOST_LOCKER_ECONOMY === $service) {
            $name = sprintf('%s (%s)', $name, $shipment['commercial_product_identifier']);
        }

        return $name;
    }

    public function displaySendingMethodName($method)
    {
        $list = $this->getSendingMethodList();

        return isset($list[$method]) ? $list[$method] : $method;
    }

    public function displayTrackingUrl($trackingNumber)
    {
        return sprintf(
            '<a href="%s" target="_blank">%s</a>',
            str_replace('@', $trackingNumber, CarrierUpdater::TRACKING_URL),
            $trackingNumber
        );
    }

    public function displayOrderReference($reference, $row)
    {
        return sprintf(
            '<a href="%s">%s</a>',
            $this->link->getAdminLink('AdminOrders', true, [], [
                'vieworder' => true,
                'id_order' => $row['id_order'],
            ]),
            $reference
        );
    }

    public function displayCreateDispatchOrderLink($token, $id)
    {
        if (!array_key_exists('createDispatchOrder', self::$cache_lang)) {
            self::$cache_lang['createDispatchOrder'] = $this->module->l('Create dispatch order', self::TRANSLATION_SOURCE);
        }

        return $this->displayLink($token, $id, 'createDispatchOrder', 'truck');
    }

    public function displayPrintLabelLink($token, $id)
    {
        if (!array_key_exists('printLabel', self::$cache_lang)) {
            self::$cache_lang['printLabel'] = $this->module->l('Print label', self::TRANSLATION_SOURCE);
        }

        return $this->displayLink($token, $id, 'printLabel');
    }

    public function displayPrintReturnLabelLink($token, $id)
    {
        if (!array_key_exists('printReturnLabel', self::$cache_lang)) {
            self::$cache_lang['printReturnLabel'] = $this->module->l('Print return label', self::TRANSLATION_SOURCE);
        }

        if (!isset($this->parcelLockerShipmentIndex)) {
            $this->parcelLockerShipmentIndex = [];

            foreach ($this->_list as $item) {
                if (in_array($item['service'], Service::LOCKER_CARRIER_SERVICES, true)) {
                    $this->parcelLockerShipmentIndex[$item['id_shipment']] = true;
                }
            }
        }

        return $this->displayLink(
            $token,
            $id,
            'printReturnLabel',
            'print',
            isset($this->parcelLockerShipmentIndex[$id]) ? $this->getSzybkieZwrotyUrl() : null
        );
    }

    public function displayPrintDispatchOrderLink($token, $id)
    {
        if (!array_key_exists('printDispatchOrder', self::$cache_lang)) {
            self::$cache_lang['printDispatchOrder'] = $this->module->l('Print dispatch order', self::TRANSLATION_SOURCE);
        }

        return $this->displayLink($token, $id, 'printDispatchOrder');
    }

    protected function renderNavTabs()
    {
        /** @var ShipmentNavTabs $view */
        $view = $this->module->getService('inpost.shipping.views.shipment_nav_tabs');

        return $view->render();
    }

    protected function handleException(Exception $exception)
    {
        if (!$exception instanceof LabelNotFoundException) {
            parent::handleException($exception);

            return;
        }

        if ($availabilityDate = $exception->getAvailabilityDate()) {
            $message = sprintf(
                $this->module->l('Label cannot be generated before %s.', self::TRANSLATION_SOURCE),
                $availabilityDate->format($this->context->language->date_format_lite)
            );
        } else {
            $message = $this->module->l('There was a problem with label generation.', self::TRANSLATION_SOURCE);
        }

        $this->errors[] = $message;
    }
}
