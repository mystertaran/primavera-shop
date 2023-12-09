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

namespace InPost\Shipping\Configuration;

use Carbon\Carbon;

class CarriersConfiguration extends ResettableConfiguration
{
    const WEEKEND_DELIVERY_START_DAY = 'INPOST_SHIPPING_WEEKEND_DELIVERY_START_DAY';
    const WEEKEND_DELIVERY_START_HOUR = 'INPOST_SHIPPING_WEEKEND_DELIVERY_START_HOUR';
    const WEEKEND_DELIVERY_END_DAY = 'INPOST_SHIPPING_WEEKEND_DELIVERY_END_DAY';
    const WEEKEND_DELIVERY_END_HOUR = 'INPOST_SHIPPING_WEEKEND_DELIVERY_END_HOUR';
    const SERVICE_DEFAULT_SHIPMENT_DIMENSIONS = 'INPOST_SHIPPING_SERVICE_DEFAULT_SHIPMENT_DIMENSIONS';
    const SERVICE_DEFAULT_DIMENSION_TEMPLATES = 'INPOST_SHIPPING_SERVICE_DEFAULT_DIMENSION_TEMPLATES';
    const SERVICE_DEFAULT_SENDING_METHODS = 'INPOST_SHIPPING_SERVICE_DEFAULT_SENDING_METHODS';

    protected $defaultDimensions;
    protected $defaultTemplates;
    protected $defaultSendingMethods;

    public function getWeekendDeliveryStartDay()
    {
        return (int) $this->get(self::WEEKEND_DELIVERY_START_DAY);
    }

    public function setWeekendDeliveryStartDay($day)
    {
        return $this->set(self::WEEKEND_DELIVERY_START_DAY, (int) $day);
    }

    public function getWeekendDeliveryStartHour()
    {
        return (string) $this->get(self::WEEKEND_DELIVERY_START_HOUR);
    }

    public function setWeekendDeliveryStartHour($hour)
    {
        return $this->set(self::WEEKEND_DELIVERY_START_HOUR, $hour);
    }

    public function getWeekendDeliveryEndDay()
    {
        return (int) $this->get(self::WEEKEND_DELIVERY_END_DAY);
    }

    public function setWeekendDeliveryEndDay($day)
    {
        return $this->set(self::WEEKEND_DELIVERY_END_DAY, (int) $day);
    }

    public function getWeekendDeliveryEndHour()
    {
        return (string) $this->get(self::WEEKEND_DELIVERY_END_HOUR);
    }

    public function setWeekendDeliveryEndHour($hour)
    {
        return $this->set(self::WEEKEND_DELIVERY_END_HOUR, $hour);
    }

    public function getDefaultShipmentDimensions($service = null)
    {
        if (!isset($this->defaultDimensions)) {
            $this->defaultDimensions = json_decode($this->get(self::SERVICE_DEFAULT_SHIPMENT_DIMENSIONS), true) ?: [];
        }

        return $service
            ? (isset($this->defaultDimensions[$service]) ? $this->defaultDimensions[$service] : null)
            : $this->defaultDimensions;
    }

    public function setDefaultShipmentDimensions($service, array $dimensions)
    {
        $this->defaultDimensions = $this->updateServiceDefaults(
            $this->getDefaultShipmentDimensions(),
            $service,
            $dimensions
        );

        return $this->set(self::SERVICE_DEFAULT_SHIPMENT_DIMENSIONS, json_encode($this->defaultDimensions));
    }

    public function getDefaultDimensionTemplates($service = null)
    {
        if (!isset($this->defaultTemplates)) {
            $this->defaultTemplates = json_decode($this->get(self::SERVICE_DEFAULT_DIMENSION_TEMPLATES), true) ?: [];
        }

        return $service
            ? (isset($this->defaultTemplates[$service]) ? $this->defaultTemplates[$service] : null)
            : $this->defaultTemplates;
    }

    public function setDefaultDimensionTemplate($service, $template)
    {
        $this->defaultTemplates = $this->updateServiceDefaults(
            $this->getDefaultDimensionTemplates(),
            $service,
            $template
        );

        return $this->set(self::SERVICE_DEFAULT_DIMENSION_TEMPLATES, json_encode($this->defaultTemplates));
    }

    public function getDefaultSendingMethods($service = null)
    {
        if (!isset($this->defaultSendingMethods)) {
            $this->defaultSendingMethods = json_decode($this->get(self::SERVICE_DEFAULT_SENDING_METHODS), true) ?: [];
        }

        return $service
            ? (isset($this->defaultSendingMethods[$service]) ? $this->defaultSendingMethods[$service] : null)
            : $this->defaultSendingMethods;
    }

    public function setDefaultSendingMethod($service, $sendingMethod)
    {
        $this->defaultSendingMethods = $this->updateServiceDefaults(
            $this->getDefaultSendingMethods(),
            $service,
            $sendingMethod
        );

        return $this->set(self::SERVICE_DEFAULT_SENDING_METHODS, json_encode($this->defaultSendingMethods));
    }

    public function setDefaults()
    {
        return $this->setWeekendDeliveryStartDay(Carbon::THURSDAY)
            && $this->setWeekendDeliveryStartHour('16:00:00')
            && $this->setWeekendDeliveryEndDay(Carbon::SATURDAY)
            && $this->setWeekendDeliveryEndHour('10:00:00');
    }

    private function updateServiceDefaults(array $defaults, $service, $newValue)
    {
        if (!empty($newValue)) {
            $defaults[$service] = $newValue;
        } else {
            unset($defaults[$service]);
        }

        return $defaults;
    }
}
