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

class InPostDispatchOrderModel extends ObjectModel
{
    public $id_dispatch_point;
    public $shipx_dispatch_order_id;
    public $number;
    public $price;
    public $status;
    public $date_add;

    public static $definition = [
        'table' => 'inpost_dispatch_order',
        'primary' => 'id_dispatch_order',
        'fields' => [
            'id_dispatch_point' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'allow_null' => true,
            ],
            'shipx_dispatch_order_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
                'allow_null' => true,
            ],
            'number' => [
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'allow_null' => true,
            ],
            'price' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'allow_null' => true,
            ],
            'status' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 64,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
        ],
        'associations' => [
            'shipments' => [
                'type' => self::HAS_MANY,
                'field' => 'id_dispatch_order',
                'foreign_field' => 'id_dispatch_order',
                'object' => 'InPostShipmentModel',
            ],
        ],
    ];

    public function add($auto_date = true, $null_values = true)
    {
        return parent::add($auto_date, $null_values);
    }

    public function update($null_values = true)
    {
        return parent::update($null_values);
    }

    /** @return self[] */
    public static function getDispatchOrders($sandbox, $organizationId, array $ids = [])
    {
        $query = (new DbQuery())
            ->select('do.*')
            ->from('inpost_dispatch_order', 'do')
            ->innerJoin('inpost_shipment', 's', 's.id_dispatch_order = do.id_dispatch_order')
            ->where('s.sandbox = ' . ($sandbox ? 1 : 0))
            ->where('s.organization_id = ' . (int) $organizationId);

        if (!empty($ids)) {
            $query->where('id_dispatch_order IN (' . implode(',', array_map('intval', $ids)) . ')');
        }

        return self::hydrateCollection(
            self::class,
            Db::getInstance()->executeS($query)
        );
    }
}
