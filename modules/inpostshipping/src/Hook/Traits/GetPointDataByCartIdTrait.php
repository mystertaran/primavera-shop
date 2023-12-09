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

namespace InPost\Shipping\Hook\Traits;

use InPost\Shipping\DataProvider\CustomerChoiceDataProvider;
use InPost\Shipping\DataProvider\PointDataProvider;
use InPost\Shipping\Hook\AbstractHook;
use InPost\Shipping\ShipX\Resource\Point;
use InPost\Shipping\ShipX\Resource\Service;

/** @mixin AbstractHook */
trait GetPointDataByCartIdTrait
{
    /**
     * @param int $cartId
     *
     * @return Point|null
     */
    public function getPointDataByCartId($cartId)
    {
        /** @var CustomerChoiceDataProvider $customerChoiceDataProvider */
        $customerChoiceDataProvider = $this->module->getService('inpost.shipping.data_provider.customer_choice');

        if (
            null === ($customerChoice = $customerChoiceDataProvider->getDataByCartId($cartId)) ||
            null === $customerChoice->point ||
            !in_array($customerChoice->service, Service::LOCKER_SERVICES, true)
        ) {
            return null;
        }

        /** @var PointDataProvider $pointDataProvider */
        $pointDataProvider = $this->module->getService('inpost.shipping.data_provider.point');

        return $pointDataProvider->getPointData($customerChoice->point);
    }
}
