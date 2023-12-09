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

namespace InPost\Shipping;

use Carbon\Carbon;
use InPost\Shipping\Configuration\CarriersConfiguration;

class TimeChecker
{
    protected $configuration;
    protected $enableWeekendDelivery;

    public function __construct(CarriersConfiguration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function shouldEnableWeekendDelivery()
    {
        if (!isset($this->enableWeekendDelivery)) {
            $this->enableWeekendDelivery = $this->isCurrentDateInWeekendDeliveryRange();
        }

        return $this->enableWeekendDelivery;
    }

    private function isCurrentDateInWeekendDeliveryRange()
    {
        $startDay = $this->configuration->getWeekendDeliveryStartDay();
        $startHour = $this->configuration->getWeekendDeliveryStartHour();
        $endDay = $this->configuration->getWeekendDeliveryEndDay();
        $endHour = $this->configuration->getWeekendDeliveryEndHour();

        $now = Carbon::now();

        $timeStart = $now->copy();
        if ($now->dayOfWeek !== $startDay || $startHour >= $now->toTimeString()) {
            $timeStart->previous($startDay);
        }
        $timeStart->setTimeFromTimeString($startHour);

        $timeEnd = $now->copy();
        if ($now->dayOfWeek !== $endDay || $endHour < $now->toTimeString()) {
            $timeEnd->next($endDay);
        }
        $timeEnd->setTimeFromTimeString($endHour);

        return $timeStart->diffInDays($timeEnd) < 7;
    }
}
