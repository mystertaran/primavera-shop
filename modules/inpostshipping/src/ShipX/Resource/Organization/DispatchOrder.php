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

namespace InPost\Shipping\ShipX\Resource\Organization;

use Carbon\Carbon;
use InPost\Shipping\Api\Resource\Traits\CreateTrait;
use InPost\Shipping\Api\Resource\Traits\DeleteTrait;
use InPost\Shipping\Api\Resource\Traits\GetTrait;
use InPost\Shipping\Api\Resource\Traits\UpdateTrait;
use InPost\Shipping\ShipX\Resource\Address;
use InPost\Shipping\ShipX\Resource\Traits\GetCollectionTrait;

/**
 * @property int $id
 * @property string $status
 * @property Carbon $created_at
 * @property Address $address
 * @property array $shipments
 * @property string $comment
 * @property float|null $price
 * @property int $external_id
 */
class DispatchOrder extends OrganizationResource
{
    use GetCollectionTrait;
    use GetTrait;
    use CreateTrait;
    use UpdateTrait;
    use DeleteTrait;

    const BASE_PATH = '/v1/organizations/{organization_id}/dispatch_orders';

    protected static $casts = [
        'created_at' => 'datetime',
        'address' => Address::class,
    ];

    public static function getPrintout($id, array $options = [])
    {
        $query = [
            'dispatch_order_id' => $id,
        ];

        return self::getPrintouts($query, $options);
    }

    public static function getPrintoutsByShipmentIds(array $shipmentIds, array $options = [])
    {
        $query = [
            'shipment_ids' => $shipmentIds,
        ];

        return self::getPrintouts($query, $options);
    }

    protected static function getPrintouts(array $queryParams, array $options)
    {
        $path = self::BASE_PATH . '/printouts';

        return self::cast([])
            ->getRequestFactory()
            ->createRequest('GET', $path, $options)
            ->setQueryParams($queryParams)
            ->send();
    }
}
