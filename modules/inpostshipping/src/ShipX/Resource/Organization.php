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

use Carbon\Carbon;
use InPost\Shipping\Api\Resource\Traits\GetTrait;
use InPost\Shipping\ShipX\Resource\Traits\GetCollectionTrait;

/**
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string $tax_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string[] $services
 * @property string $bank_account_number
 * @property Address $address
 */
class Organization extends AuthorizedResource
{
    use GetCollectionTrait;
    use GetTrait;

    const BASE_PATH = '/v1/organizations';

    protected static $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'address' => Address::class,
    ];
}
