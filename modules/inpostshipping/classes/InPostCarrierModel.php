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

use InPost\Shipping\ShipX\Resource\Service;

class InPostCarrierModel extends ObjectModel
{
    public $force_id = true;

    public $service;
    public $commercial_product_identifier;
    public $cod = false;
    public $weekend_delivery = false;
    public $use_product_dimensions = false;

    public static $definition = [
        'table' => 'inpost_carrier',
        'primary' => 'id_reference',
        'fields' => [
            'service' => [
                'type' => self::TYPE_STRING,
                'values' => Service::SERVICES,
            ],
            'commercial_product_identifier' => [
                'type' => self::TYPE_STRING,
                'size' => 8,
                'allow_null' => true,
            ],
            'cod' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'weekend_delivery' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
            'use_product_dimensions' => [
                'type' => self::TYPE_BOOL,
                'validate' => 'isBool',
            ],
        ],
    ];

    public function add($auto_date = true, $null_values = false)
    {
        $id_reference = $this->id;

        if ($result = parent::add($auto_date, $null_values)) {
            $this->id = $id_reference;
        }

        return $result;
    }

    public function delete()
    {
        return $this->unlinkAndDisableCarrier()
            && parent::delete();
    }

    public function getCarrier()
    {
        return Carrier::getCarrierByReference($this->id) ?: null;
    }

    public static function getDataByCarrierId($id_carrier)
    {
        static $carriers;

        if (!isset($carriers)) {
            $query = (new DbQuery())
                ->select('ic.*, c.id_carrier')
                ->from('inpost_carrier', 'ic')
                ->innerJoin('carrier', 'c', 'c.id_reference = ic.id_reference')
                ->where('c.deleted = 0')
                ->where('c.active = 1');

            foreach (Db::getInstance()->executeS($query) as $row) {
                $carriers[$row['id_carrier']] = [
                    'id_carrier' => (int) $row['id_carrier'],
                    'cashOnDelivery' => (bool) $row['cod'],
                    'commercialProductIdentifier' => $row['commercial_product_identifier'],
                    'service' => $row['service'],
                    'lockerService' => in_array($row['service'], Service::LOCKER_SERVICES, true),
                    'weekendDelivery' => (bool) $row['weekend_delivery'],
                ];
            }
        }

        return isset($carriers[$id_carrier]) ? $carriers[$id_carrier] : null;
    }

    /** @return self[] */
    public static function getNonDeletedCarriers()
    {
        $subQuery = (new DbQuery())
            ->from('carrier')
            ->where('id_reference = ic.id_reference')
            ->where('deleted = 0');

        $query = (new DbQuery())
            ->from('inpost_carrier', 'ic')
            ->where('EXISTS (' . $subQuery . ')');

        return self::hydrateCollection(
            self::class,
            Db::getInstance()->executeS($query)
        );
    }

    private function unlinkAndDisableCarrier()
    {
        if (!$carrier = $this->getCarrier()) {
            return true;
        }

        $carrier->active = false;
        $carrier->is_module = false;
        $carrier->external_module_name = null;

        return $carrier->update();
    }
}
