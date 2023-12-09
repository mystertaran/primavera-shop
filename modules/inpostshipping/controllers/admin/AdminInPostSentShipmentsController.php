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

use InPost\Shipping\Install\Tabs;
use InPost\Shipping\ShipX\Resource\Status;

require_once dirname(__FILE__) . '/AdminInPostShipmentsController.php';

class AdminInPostSentShipmentsController extends AdminInPostShipmentsController
{
    const TRANSLATION_SOURCE = 'AdminInPostSentShipmentsController';

    public function __construct()
    {
        parent::__construct();

        $this->_select .= ', COALESCE(sl.title, a.status) as status_title, sl.description';
        $this->_join .= '
            LEFT JOIN `' . _DB_PREFIX_ . 'inpost_shipment_status` s ON s.name = a.status
            LEFT JOIN `' . _DB_PREFIX_ . 'inpost_shipment_status_lang` sl
                ON sl.id_status = s.id_status AND sl.id_lang = ' . (int) $this->context->language->id;
        $this->_where .= ' AND a.status NOT IN ("' . implode('","', Status::NOT_SENT_STATUSES) . '")';
    }

    protected function getFieldsList()
    {
        $fields = parent::getFieldsList();

        return array_merge(
            array_slice($fields, 0, 2),
            [
                'status_title' => [
                    'title' => $this->module->l('State', self::TRANSLATION_SOURCE),
                    'type' => 'select',
                    'list' => $this->getStatusList(),
                    'filter_key' => 'a!status',
                    'callback' => 'displayStatus',
                ],
            ],
            array_slice($fields, 2)
        );
    }

    protected function getStatusList()
    {
        $list = [];

        $collection = new PrestaShopCollection(InPostShipmentStatusModel::class, $this->context->language->id);

        /** @var InPostShipmentStatusModel $status */
        foreach ($collection as $status) {
            $list[$status->name] = $status->title;
        }

        return $list;
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

    public function displayStatus($status, $row)
    {
        if ($description = $row['description']) {
            return sprintf(
                '<a data-toggle="tooltip" title="%s">%s</a>',
                $description,
                $status
            );
        }

        return $status;
    }
}
