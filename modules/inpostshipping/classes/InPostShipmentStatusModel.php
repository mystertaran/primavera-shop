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

class InPostShipmentStatusModel extends ObjectModel
{
    public $name;
    public $title;
    public $description;

    public static $definition = [
        'table' => 'inpost_shipment_status',
        'primary' => 'id_status',
        'multilang' => true,
        'fields' => [
            'name' => [
                'type' => self::TYPE_STRING,
                'validate' => 'isTableOrIdentifier',
                'size' => 64,
            ],
            'title' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'size' => 128,
            ],
            'description' => [
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isCleanHtml',
                'size' => 512,
            ],
        ],
    ];

    public static function getStatusByName($name)
    {
        $collection = (new PrestaShopCollection(self::class))
            ->where('name', 'LIKE', $name);

        return $collection->getFirst();
    }
}
