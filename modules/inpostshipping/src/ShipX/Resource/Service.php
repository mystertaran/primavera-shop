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
 * @property string $id
 * @property string $name
 * @property string $description
 * @property array $additional_services
 */
class Service extends ShipXResource
{
    use GetAllTrait;

    const BASE_PATH = '/v1/services';

    const INPOST_LOCKER_STANDARD = 'inpost_locker_standard';
    const INPOST_LOCKER_ECONOMY = 'inpost_locker_economy';
    const INPOST_COURIER_C2C = 'inpost_courier_c2c';
    const INPOST_COURIER_STANDARD = 'inpost_courier_standard';
    const INPOST_COURIER_EXPRESS_1000 = 'inpost_courier_express_1000';
    const INPOST_COURIER_EXPRESS_1200 = 'inpost_courier_express_1200';
    const INPOST_COURIER_EXPRESS_1700 = 'inpost_courier_express_1700';
    const INPOST_COURIER_LOCAL_STANDARD = 'inpost_courier_local_standard';
    const INPOST_COURIER_LOCAL_EXPRESS = 'inpost_courier_local_express';
    const INPOST_COURIER_LOCAL_SUPER_EXPRESS = 'inpost_courier_local_super_express';
    const INPOST_COURIER_PALETTE = 'inpost_courier_palette';

    const INPOST_LOCKER_CUSTOMER_SERVICE_POINT = 'inpost_locker_customer_service_point';

    const COMMERCIAL_PRODUCT_IDENTIFIER_2D = '2D';
    const COMMERCIAL_PRODUCT_IDENTIFIER_FF = 'FF';
    const COMMERCIAL_PRODUCT_IDENTIFIER_DI = 'DI';

    const SERVICES = [
        self::INPOST_LOCKER_STANDARD,
        self::INPOST_LOCKER_ECONOMY,
        self::INPOST_COURIER_C2C,
        self::INPOST_COURIER_STANDARD,
        self::INPOST_COURIER_EXPRESS_1000,
        self::INPOST_COURIER_EXPRESS_1200,
        self::INPOST_COURIER_EXPRESS_1700,
        self::INPOST_COURIER_LOCAL_STANDARD,
        self::INPOST_COURIER_LOCAL_EXPRESS,
        self::INPOST_COURIER_LOCAL_SUPER_EXPRESS,
        self::INPOST_COURIER_PALETTE,
    ];

    const LOCKER_CARRIER_SERVICES = [
        self::INPOST_LOCKER_STANDARD,
        self::INPOST_LOCKER_ECONOMY,
        self::INPOST_COURIER_C2C,
    ];

    const LOCKER_SERVICES = [
        self::INPOST_LOCKER_STANDARD,
        self::INPOST_LOCKER_ECONOMY,
    ];

    const COMMERCIAL_PRODUCT_IDENTIFIERS = [
        self::COMMERCIAL_PRODUCT_IDENTIFIER_2D,
        self::COMMERCIAL_PRODUCT_IDENTIFIER_FF,
        self::COMMERCIAL_PRODUCT_IDENTIFIER_DI,
    ];
}
