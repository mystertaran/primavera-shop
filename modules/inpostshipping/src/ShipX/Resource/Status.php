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

use InPost\Shipping\ShipX\Resource\Traits\GetAllTrait;

/**
 * @property string $name
 * @property string $title
 * @property string $description
 */
class Status extends ShipXResource
{
    use GetAllTrait;

    const BASE_PATH = '/v1/statuses';

    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_DELIVERED = 'delivered';

    const NOT_SENT_STATUSES = [
        'created',
        'offers_prepared',
        'offer_selected',
        self::STATUS_CONFIRMED,
    ];

    const FINAL_STATUSES = [
        'canceled',
        self::STATUS_DELIVERED,
        'not_found',
    ];
}
