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

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Handler\DispatchOrder\UpdateDispatchOrderHandler;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\Presenter\DispatchPointPresenter;
use InPost\Shipping\ShipX\Resource\Organization\DispatchOrder;
use InPost\Shipping\Views\ShipmentNavTabs;

require_once dirname(__FILE__) . '/InPostShippingAdminController.php';

class AdminInPostDispatchOrdersController extends InPostShippingAdminController
{
    const TRANSLATION_SOURCE = 'AdminInPostDispatchOrdersController';

    protected $dispatchPointList;

    public function __construct()
    {
        $this->table = 'inpost_dispatch_order';
        $this->identifier = 'id_dispatch_order';
        $this->bootstrap = true;
        $this->list_no_link = true;

        parent::__construct();

        $this->className = InPostDispatchOrderModel::class;

        /** @var ShipXConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.shipx');

        $this->_select = ' GROUP_CONCAT(s.tracking_number) as shipments, o.id_currency';
        $this->_join = '
            LEFT JOIN `' . _DB_PREFIX_ . 'inpost_dispatch_point` dp ON dp.id_dispatch_point= a.id_dispatch_point
            INNER JOIN `' . _DB_PREFIX_ . 'inpost_shipment` s ON s.id_dispatch_order = a.id_dispatch_order
            INNER JOIN `' . _DB_PREFIX_ . 'orders` o ON o.id_order = s.id_order';
        $this->_where = '
            AND s.sandbox = ' . ($configuration->isSandboxModeEnabled() ? 1 : 0) . '
            AND s.organization_id = ' . $configuration->getOrganizationId();
        $this->_group = 'GROUP BY a.id_dispatch_order';
        $this->_orderWay = 'desc';

        $this->fields_list = [
            'number' => [
                'title' => $this->module->l('Dispatch order number', self::TRANSLATION_SOURCE),
            ],
            'id_dispatch_point' => [
                'title' => $this->module->l('Dispatch point', self::TRANSLATION_SOURCE),
                'type' => 'select',
                'list' => $this->getDispatchPointList(),
                'filter_key' => 'a!id_dispatch_point',
                'callback' => 'displayDispatchPoint',
            ],
            'price' => [
                'type' => 'price',
                'currency' => true,
                'title' => $this->module->l('Price', self::TRANSLATION_SOURCE),
                'search' => false,
                'class' => 'fixed-width-xs',
            ],
            'status' => [
                'title' => $this->module->l('State', self::TRANSLATION_SOURCE),
                'filter_key' => 'a!status',
            ],
            'shipments' => [
                'title' => $this->module->l('Associated shipments', self::TRANSLATION_SOURCE),
                'search' => false,
                'callback' => 'displayShipments',
            ],
            'date_add' => [
                'title' => $this->module->l('Created at', self::TRANSLATION_SOURCE),
                'type' => 'datetime',
                'filter_key' => 'a!date_add',
            ],
        ];

        $this->addRowAction('print');
    }

    protected function getDispatchPointList()
    {
        if (!isset($this->dispatchPointList)) {
            $this->dispatchPointList = [];

            /** @var DispatchPointPresenter $presenter */
            $presenter = $this->module->getService('inpost.shipping.presenter.dispatch_point');
            $collection = (new PrestaShopCollection(InPostDispatchPointModel::class))
                ->where('deleted', '=', 0);

            /** @var InPostDispatchPointModel $dispatchPoint */
            foreach ($collection as $dispatchPoint) {
                $this->dispatchPointList[$dispatchPoint->id] = $presenter->present($dispatchPoint);
            }
        }

        return $this->dispatchPointList;
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->module->getAssetsManager()
            ->registerJavaScripts([
                _THEME_JS_DIR_ . 'custom.js',
                'admin/tools.js',
                'admin/dispatch-orders.js',
            ])
            ->registerStyleSheets([
                'admin/table-fix.css',
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
            'desc' => $this->module->l('Refresh dispatch order statuses', self::TRANSLATION_SOURCE),
            'imgclass' => 'refresh',
        ];
    }

    public function initBreadcrumbs($tab_id = null, $tabs = null)
    {
        parent::initBreadcrumbs($tab_id, $tabs);

        if (!$this->shopContext->is17()) {
            $this->breadcrumbs = array_merge([
                $this->module->l('InPost shipments', Tabs::TRANSLATION_SOURCE),
            ], $this->breadcrumbs);
        }
    }

    public function processRefreshStatuses(array $dispatchOrderIds = [])
    {
        /** @var UpdateDispatchOrderHandler $handler */
        $handler = $this->module->getService('inpost.shipping.handler.dispatch_order.update');

        $handler->handle($dispatchOrderIds);

        $this->redirect_after = $this->link->getAdminLink($this->controller_name, true, [], [
            'conf' => 4,
        ]);
    }

    public function ajaxProcessPrint()
    {
        $this->processPrint();
    }

    public function processPrint()
    {
        /** @var InPostDispatchOrderModel $dispatchOrderModel */
        if ($dispatchOrderModel = $this->loadObject()) {
            $this->offerDownload(
                DispatchOrder::getPrintout($dispatchOrderModel->shipx_dispatch_order_id),
                'dispatch_order'
            );
        }
    }

    public function displayDispatchPoint($id_dispatch_point)
    {
        return $id_dispatch_point ? $this->getDispatchPointList()[$id_dispatch_point] : null;
    }

    public function displayShipments($shipments)
    {
        return str_replace(',', '<br/>', $shipments);
    }

    public function displayPrintLink($token, $id)
    {
        if (!array_key_exists('print', self::$cache_lang)) {
            self::$cache_lang['print'] = $this->module->l('Print', self::TRANSLATION_SOURCE);
        }

        return $this->displayLink($token, $id, 'print');
    }

    protected function renderNavTabs()
    {
        /** @var ShipmentNavTabs $view */
        $view = $this->module->getService('inpost.shipping.views.shipment_nav_tabs');

        return $view->render();
    }
}
