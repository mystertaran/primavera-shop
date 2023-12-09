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

namespace InPost\Shipping\Handler\DispatchOrder;

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Resource\Organization\DispatchOrder;
use InPostDispatchOrderModel;

class UpdateDispatchOrderHandler
{
    protected $shipXConfiguration;

    public function __construct(ShipXConfiguration $shipXConfiguration)
    {
        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function handle(array $ids = [])
    {
        $collection = InPostDispatchOrderModel::getDispatchOrders(
            $this->shipXConfiguration->useSandboxMode(),
            $this->shipXConfiguration->getOrganizationId(),
            $ids
        );

        if (count($collection)) {
            $dispatchOrderIds = [];
            $collectionKeyIndex = [];

            foreach ($collection as $key => $dispatchOrderModel) {
                $dispatchOrderIds[] = $dispatchOrderModel->shipx_dispatch_order_id;
                $collectionKeyIndex[$dispatchOrderModel->shipx_dispatch_order_id] = $key;
            }

            foreach (DispatchOrder::getCollection(['id' => $dispatchOrderIds]) as $dispatchOrder) {
                $dispatchOrderModel = $collection[$collectionKeyIndex[$dispatchOrder->getId()]];
                if ($dispatchOrderModel->number != $dispatchOrder->external_id ||
                    $dispatchOrderModel->status != $dispatchOrder->status
                ) {
                    if ($number = $dispatchOrder->external_id) {
                        $dispatchOrderModel->number = $number;
                    }
                    $dispatchOrderModel->status = $dispatchOrder->status;
                    $dispatchOrderModel->update();
                }
            }
        }
    }
}
