<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use InPost\Shipping\ShipX\Resource\Organization\Shipment;

class InPostProductTemplateModel extends ObjectModel
{
    public $force_id = true;

    public $template;

    public static $definition = [
        'table' => 'inpost_product_template',
        'primary' => 'id_product',
        'fields' => [
            'template' => [
                'type' => self::TYPE_STRING,
                'values' => Shipment::DIMENSION_TEMPLATES,
            ],
        ],
    ];

    public static function getTemplatesByOrderId($id_order)
    {
        $query = (new DbQuery())
            ->select('DISTINCT ipt.template')
            ->from('order_detail', 'od')
            ->innerJoin('inpost_product_template', 'ipt', 'ipt.id_product = od.product_id')
            ->where('od.id_order = ' . (int) $id_order);

        return array_column(
            Db::getInstance()->executeS($query),
            'template'
        );
    }
}
