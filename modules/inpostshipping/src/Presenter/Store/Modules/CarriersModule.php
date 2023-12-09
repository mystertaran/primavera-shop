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

namespace InPost\Shipping\Presenter\Store\Modules;

use InPost\Shipping\ChoiceProvider\WeekdayChoiceProvider;
use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\Presenter\Store\PresenterInterface;

class CarriersModule implements PresenterInterface
{
    protected $carriersConfiguration;
    protected $weekdayChoiceProvider;

    public function __construct(
        CarriersConfiguration $carriersConfiguration,
        WeekdayChoiceProvider $weekdayChoiceProvider
    ) {
        $this->carriersConfiguration = $carriersConfiguration;
        $this->weekdayChoiceProvider = $weekdayChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function present()
    {
        return [
            'carriers' => [
                'weekendDelivery' => [
                    'weekdayChoices' => $this->weekdayChoiceProvider->getChoices(),
                    'start' => [
                        'day' => $this->carriersConfiguration->getWeekendDeliveryStartDay(),
                        'hour' => $this->carriersConfiguration->getWeekendDeliveryStartHour(),
                    ],
                    'end' => [
                        'day' => $this->carriersConfiguration->getWeekendDeliveryEndDay(),
                        'hour' => $this->carriersConfiguration->getWeekendDeliveryEndHour(),
                    ],
                ],
            ],
        ];
    }
}
