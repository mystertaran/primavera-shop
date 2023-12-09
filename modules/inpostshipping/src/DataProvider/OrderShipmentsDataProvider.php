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

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Presenter\ShipmentPresenter;
use InPostShipmentModel;
use PrestaShopCollection;

class OrderShipmentsDataProvider
{
    protected $shipXConfiguration;
    protected $shipmentPresenter;

    public function __construct(
        ShipXConfiguration $shipXConfiguration,
        ShipmentPresenter $shipmentPresenter
    ) {
        $this->shipXConfiguration = $shipXConfiguration;
        $this->shipmentPresenter = $shipmentPresenter;
    }

    public function getOrderShipments($id_order)
    {
        $result = [];

        $shipments = (new PrestaShopCollection(InPostShipmentModel::class))
            ->where('sandbox', '=', $this->shipXConfiguration->useSandboxMode())
            ->where('id_order', '=', $id_order)
            ->where('organization_id', '=', $this->shipXConfiguration->getOrganizationId());

        /** @var InPostShipmentModel $shipment */
        foreach ($shipments as $shipment) {
            $result[] = $this->shipmentPresenter->present($shipment);
        }

        return $result;
    }
}
