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

use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPostShipping;

class SendingMethodTranslator
{
    const TRANSLATION_SOURCE = 'SendingMethodTranslator';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function translate($sendingMethod)
    {
        static $translations;

        if (!isset($translations)) {
            $translations = [
                SendingMethod::PARCEL_LOCKER => $this->module->l('From a Parcel Locker', self::TRANSLATION_SOURCE),
                SendingMethod::DISPATCH_ORDER => $this->module->l('Pickup by courier', self::TRANSLATION_SOURCE),
                SendingMethod::POP => $this->module->l('From a Shipment Service Point', self::TRANSLATION_SOURCE),
            ];
        }

        return isset($translations[$sendingMethod]) ? $translations[$sendingMethod] : $sendingMethod;
    }
}
