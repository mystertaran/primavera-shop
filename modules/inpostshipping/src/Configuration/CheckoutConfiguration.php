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

class CheckoutConfiguration extends ResettableConfiguration
{
    const USING_CUSTOM_CHECKOUT_MODULE = 'INPOST_SHIPPING_USING_CUSTOM_CHECKOUT_MODULE';
    const CUSTOM_CHECKOUT_CONTROLLERS = 'INPOST_SHIPPING_CUSTOM_CHECKOUT_CONTROLLERS';
    const SHOW_INPUT_EMAIL = 'INPOST_SHIPPING_SHOW_INPUT_EMAIL';
    const SHOW_INPUT_PHONE = 'INPOST_SHIPPING_SHOW_INPUT_PHONE';

    protected $customCheckoutControllers;

    public function isUsingCustomCheckoutModule()
    {
        return (bool) $this->get(self::USING_CUSTOM_CHECKOUT_MODULE);
    }

    public function setUsingCustomCheckoutModule($usingCustomCheckout)
    {
        return $this->set(self::USING_CUSTOM_CHECKOUT_MODULE, (bool) $usingCustomCheckout);
    }

    public function getShowInputEmail()
    {
        return (bool) $this->get(self::SHOW_INPUT_EMAIL);
    }

    public function setShowInputEmail($showInputEmail)
    {
        return $this->set(self::SHOW_INPUT_EMAIL, (bool) $showInputEmail);
    }

    public function getShowInputPhone()
    {
        return (bool) $this->get(self::SHOW_INPUT_PHONE);
    }

    public function setShowInputPhone($showInputPhone)
    {
        return $this->set(self::SHOW_INPUT_PHONE, (bool) $showInputPhone);
    }

    public function getCustomCheckoutControllers()
    {
        if (!isset($this->customCheckoutControllers)) {
            $this->customCheckoutControllers = json_decode($this->get(self::CUSTOM_CHECKOUT_CONTROLLERS), true) ?: [];
        }

        return $this->customCheckoutControllers;
    }

    public function setCustomCheckoutControllers(array $controllers)
    {
        if ($this->set(self::CUSTOM_CHECKOUT_CONTROLLERS, json_encode($controllers))) {
            $this->customCheckoutControllers = $controllers;

            return true;
        }

        return false;
    }
}
