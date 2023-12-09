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

namespace InPost\Shipping\Translations;

use InPost\Shipping\ShipX\Resource\Service;
use InPostShipping;

class ShippingServiceTranslator
{
    const TRANSLATION_SOURCE = 'ShippingServiceTranslator';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function translate($service)
    {
        static $translations;

        if (!isset($translations)) {
            $translations = [
                Service::INPOST_LOCKER_STANDARD => $this->module->l('Parcel station shipment - standard', self::TRANSLATION_SOURCE),
                Service::INPOST_LOCKER_ECONOMY => $this->module->l('Parcel station shipment - economic', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_C2C => $this->module->l('InPost Kurier Standard C2C courier shipment', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_STANDARD => $this->module->l('Standard courier shipment', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_EXPRESS_1000 => $this->module->l('Courier shipment with delivery until 10:00', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_EXPRESS_1200 => $this->module->l('Courier shipment with delivery until 12:00', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_EXPRESS_1700 => $this->module->l('Courier shipment with delivery until 17:00', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_LOCAL_STANDARD => $this->module->l('Local Standard courier shipment', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_LOCAL_EXPRESS => $this->module->l('Local Express courier shipment', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_LOCAL_SUPER_EXPRESS => $this->module->l('Local Super Express courier shipment', self::TRANSLATION_SOURCE),
                Service::INPOST_COURIER_PALETTE => $this->module->l('Pallet Standard courier shipment', self::TRANSLATION_SOURCE),
            ];
        }

        return isset($translations[$service]) ? $translations[$service] : $service;
    }
}
