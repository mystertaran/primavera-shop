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

class InPostCartChoiceModel extends ObjectModel
{
    public $force_id = true;

    public $service;
    public $email;
    public $phone;
    public $point;

    public static $definition = [
        'table' => 'inpost_cart_choice',
        'primary' => 'id_cart',
        'fields' => [
            'service' => [
                'type' => self::TYPE_STRING,
                'values' => Service::SERVICES,
            ],
            'email' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isEmail',
                'size' => 255,
                'allow_null' => true,
            ],
            'phone' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isPhoneNumber',
                'size' => 255,
                'allow_null' => true,
            ],
            'point' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isGenericName',
                'size' => 32,
                'allow_null' => true,
            ],
        ],
    ];

    public function add($auto_date = true, $null_values = true)
    {
        $id_cart = $this->id;

        if ($result = parent::add($auto_date, $null_values)) {
            $this->id = $id_cart;
        }

        return $result;
    }

    public function update($null_values = true)
    {
        return parent::update($null_values);
    }

    public static function formatPhone($phone)
    {
        return empty($phone) ? '' : preg_replace('/\s+/', '', $phone);
    }
}
