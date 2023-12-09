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

class InPostDispatchPointModel extends ObjectModel
{
    public $name;
    public $office_hours;
    public $phone;
    public $email;
    public $street;
    public $building_number;
    public $post_code;
    public $city;
    public $deleted = false;

    public static $definition = [
        'table' => 'inpost_dispatch_point',
        'primary' => 'id_dispatch_point',
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isGenericName',
                'size' => 255,
            ],
            'office_hours' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isCleanHtml',
                'size' => 255,
                'allow_null' => true,
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
            'street' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isAddress',
                'size' => 255,
            ],
            'building_number' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isAddress',
                'size' => 255,
            ],
            'post_code' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isPostCode',
                'size' => 6,
            ],
            'city' => [
                'type' => self::TYPE_STRING,
                'required' => true,
                'validate' => 'isCityName',
                'size' => 255,
            ],
            'deleted' => [
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

    public function delete()
    {
        if ($this->isUsed()) {
            $this->deleted = true;

            return $this->update();
        }

        return parent::delete();
    }

    protected function isUsed()
    {
        $query = (new DbQuery())
            ->from('inpost_dispatch_order')
            ->where('id_dispatch_point = ' . (int) $this->id);

        return Db::getInstance()->getValue('SELECT EXISTS (' . $query . ')');
    }
}
