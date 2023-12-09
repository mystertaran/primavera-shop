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

namespace InPost\Shipping\Presenter;

use Context;
use InPostShipmentStatusModel;
use PrestaShopCollection;

class ShipmentStatusPresenter
{
    protected $context;

    protected $statuses;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function present($status)
    {
        $this->loadStatuses();

        return isset($this->statuses[$status])
            ? $this->statuses[$status]
            : [
                'name' => $status,
                'title' => $status,
                'description' => $status,
            ];
    }

    protected function loadStatuses()
    {
        if (!isset($this->statuses)) {
            $this->statuses = [];

            $statuses = new PrestaShopCollection(
                InPostShipmentStatusModel::class,
                $this->context->language->id
            );

            /** @var InPostShipmentStatusModel $status */
            foreach ($statuses as $status) {
                $this->statuses[$status->name] = [
                    'name' => $status->name,
                    'title' => $status->title,
                    'description' => $status->description,
                ];
            }
        }

        return $this;
    }
}
