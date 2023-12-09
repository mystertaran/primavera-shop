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

use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\ShipX\Resource\Service;

class InPostShipmentModel extends ObjectModel
{
    public $id_order;
    public $organization_id;
    public $sandbox = false;
    public $shipx_shipment_id;
    public $reference;
    public $email;
    public $phone;
    public $service;
    public $commercial_product_identifier;
    public $sending_method;
    public $sending_point;
    public $weekend_delivery;
    public $template;
    public $dimensions;
    public $id_dispatch_order;
    public $target_point;
    public $cod_amount;
    public $insurance_amount;
    public $tracking_number;
    public $status;
    public $price;
    public $label_printed = false;
    public $date_add;

    /** @var Order */
    protected $order;

    public static $definition = [
        'table' => 'inpost_shipment',
        'primary' => 'id_shipment',
        'fields' => [
            'id_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'organization_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'sandbox' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'shipx_shipment_id' => [
                'type' => self::TYPE_INT,
                'validate' => 'isUnsignedId',
            ],
            'reference' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 100,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 255,
                'required' => true,
            ],
            'phone' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isPhoneNumber',
                'size' => 255,
                'required' => true,
            ],
            'service' => [
                'type' => self::TYPE_STRING,
                'values' => Service::SERVICES,
            ],
            'commercial_product_identifier' => [
                'type' => self::TYPE_STRING,
                'size' => 8,
                'allow_null' => true,
            ],
            'sending_method' => [
                'type' => self::TYPE_STRING,
                'values' => SendingMethod::SENDING_METHODS,
                'allow_null' => true,
            ],
            'sending_point' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 32,
                'allow_null' => true,
            ],
            'weekend_delivery' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isBool',
                'allow_null' => true,
            ],
            'template' => [
                'type' => self::TYPE_STRING,
                'values' => Shipment::DIMENSION_TEMPLATES,
                'allow_null' => true,
            ],
            'dimensions' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 255,
                'allow_null' => true,
            ],
            'id_dispatch_order' => [
                'type' => self::TYPE_INT,
                'validate' => 'isNullOrUnsignedId',
                'allow_null' => true,
            ],
            'target_point' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 32,
                'allow_null' => true,
            ],
            'cod_amount' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'allow_null' => true,
            ],
            'insurance_amount' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'allow_null' => true,
            ],
            'tracking_number' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 24,
                'allow_null' => true,
            ],
            'status' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 64,
                'allow_null' => true,
            ],
            'price' => [
                'type' => self::TYPE_FLOAT,
                'validate' => 'isPrice',
                'allow_null' => true,
            ],
            'date_add' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
            ],
            'label_printed' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
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

    public function setOrder(Order $order)
    {
        if (!Validate::isLoadedObject($order)) {
            return;
        }

        if (isset($this->id_order) && (int) $order->id !== (int) $this->id_order) {
            return;
        }

        $this->order = $order;
        $this->id_order = $this->order->id;
    }

    public function getOrder()
    {
        if (!isset($this->order) && Validate::isLoadedObject($this)) {
            $this->order = new Order($this->id_order);
        }

        return $this->order;
    }

    public function updateOrderTrackingNumber()
    {
        if (empty($this->tracking_number) || !$order = $this->getOrder()) {
            return;
        }

        $order->shipping_number = $this->tracking_number;
        $order->update();

        $orderCarrier = new OrderCarrier($order->getIdOrderCarrier());
        $orderCarrier->tracking_number = $this->tracking_number;
        $orderCarrier->update();

        if (method_exists($orderCarrier, 'sendInTransitEmail')) {
            $orderCarrier->sendInTransitEmail($order);
        }
    }

    public static function getSkipPrintDispatchOrderList($organizationId, $sandbox)
    {
        $query = self::getQuery($sandbox, $organizationId)
            ->select('id_shipment')
            ->where('id_dispatch_order IS NULL OR id_dispatch_order = 0');

        return self::getSkipList($query);
    }

    public static function getSkipCreateDispatchOrderList($organizationId, $sandbox)
    {
        $query = self::getQuery($sandbox, $organizationId)
            ->select('id_shipment')
            ->where(implode(' OR ', [
                'sending_method <> "' . SendingMethod::DISPATCH_ORDER . '"',
                'id_dispatch_order IS NOT NULL AND id_dispatch_order <> 0',
            ]));

        return self::getSkipList($query);
    }

    protected static function getSkipList(DbQuery $query)
    {
        $ids = [];

        foreach (Db::getInstance()->executeS($query) as $row) {
            $ids[$row['id_shipment']] = $row['id_shipment'];
        }

        return $ids;
    }

    /** @return self[] */
    public static function getByIds(array $ids, $sandbox, $organizationId)
    {
        return (new PrestaShopCollection(self::class))
            ->where('id_shipment', '=', $ids)
            ->where('sandbox', '=', $sandbox)
            ->where('organization_id', '=', $organizationId)
            ->getResults();
    }

    public static function getShipXShipmentIds(array $ids, $sandbox, $withoutLockerCarriers = false, $withDispatchOrders = false)
    {
        if (empty($ids)) {
            return [];
        }

        $query = self::getQuery($sandbox)
            ->select('shipx_shipment_id')
            ->where('id_shipment IN (' . implode(',', array_map('intval', $ids)) . ')');

        if ($withoutLockerCarriers) {
            $query->where('service NOT IN ("' . implode('","', Service::LOCKER_CARRIER_SERVICES) . '")');
        }

        if ($withDispatchOrders) {
            $query->where('id_dispatch_order IS NOT NULL')
                ->where('id_dispatch_order <> 0');
        }

        return array_column(Db::getInstance()->executeS($query), 'shipx_shipment_id');
    }

    public static function getShipmentIdsByOrderIds(array $ids, $sandbox, $organizationId)
    {
        if (empty($ids)) {
            return [];
        }

        $query = self::getQuery($sandbox, $organizationId)
            ->select('id_shipment')
            ->where('id_order IN (' . implode(',', array_map('intval', $ids)) . ')');

        return array_column(Db::getInstance()->executeS($query), 'id_shipment');
    }

    protected static function getQuery($sandbox, $organizationId = null)
    {
        $query = (new DbQuery())
            ->from('inpost_shipment')
            ->where('sandbox = ' . ($sandbox ? 1 : 0));

        if ($organizationId) {
            $query->where('organization_id = ' . (int) $organizationId);
        }

        return $query;
    }

    public function validateField($field, $value, $id_lang = null, $skip = [], $human_errors = false)
    {
        if ($value === null &&
            isset($this->def['fields'][$field]['allow_null']) &&
            $this->def['fields'][$field]['allow_null']
        ) {
            return true;
        }

        return parent::validateField($field, $value, $id_lang, $skip, $human_errors);
    }
}
