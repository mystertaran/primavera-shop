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

namespace InPost\Shipping\ShipX\Resource;

use InPost\Shipping\Api\Resource\Traits\GetTrait;
use InPost\Shipping\ShipX\Resource\Traits\GetCollectionTrait;

/**
 * @property string $name
 * @property array $type
 * @property string $status
 * @property array $location
 * @property string $location_type
 * @property string $location_description
 * @property string $location_description_1
 * @property string $location_description_2
 * @property int|null $distance
 * @property string $opening_hours
 * @property array $address
 * @property array $address_details
 * @property string $phone_number
 * @property string $payment_point_descr
 * @property array $functions
 * @property int $partner_id
 * @property bool $is_next
 * @property bool $payment_available
 * @property array $payment_type
 * @property int $virtual
 * @property array $recommended_low_interest_box_machines_list
 * @property bool $location_247
 */
class Point extends ShipXResource
{
    use GetCollectionTrait;
    use GetTrait;

    const BASE_PATH = '/v1/points';

    const TYPE_PARCEL_LOCKER = 'parcel_locker';
    const TYPE_POP = 'pop';
    const TYPE_PARCEL_LOCKER_ONLY = 'parcel_locker_only';
    const TYPE_PARCEL_LOCKER_SUPERPOP = 'parcel_locker_superpop';

    const FUNCTION_PARCEL = 'parcel';
    const FUNCTION_PARCEL_COLLECT = 'parcel_collect';
    const FUNCTION_PARCEL_SEND = 'parcel_send';

    const PARTNER_ID_POP = 33;

    const FUNCTIONS_SEND = [
        self::FUNCTION_PARCEL,
        self::FUNCTION_PARCEL_SEND,
    ];

    const FUNCTIONS_COLLECT = [
        self::FUNCTION_PARCEL,
        self::FUNCTION_PARCEL_COLLECT,
    ];

    public static function getIdField()
    {
        return 'name';
    }
}
