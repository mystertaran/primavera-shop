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

namespace InPost\Shipping\DataProvider;

use Db;
use DbQuery;
use InPostProductTemplateModel;

class OrderDimensionsDataProvider
{
    public function getProductDimensionTemplatesByOrderId($id_order)
    {
        return InPostProductTemplateModel::getTemplatesByOrderId($id_order);
    }

    public function getLargestProductDimensionsByOrderId($id_order)
    {
        $query = (new DbQuery())
            ->select('(10 * p.depth) AS length, (10 * p.width) AS width, (10 * p.height) AS height')
            ->from('order_detail', 'od')
            ->innerJoin('product', 'p', 'p.id_product = od.product_id')
            ->where('od.id_order = ' . (int) $id_order)
            ->where('p.depth > 0 OR p.width > 0 OR p.height > 0')
            ->orderBy('(p.depth + p.width + p.height) DESC');

        return Db::getInstance()->getRow($query) ?: null;
    }
}
