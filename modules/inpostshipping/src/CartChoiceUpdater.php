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

use Address;
use Cart;
use InPost\Shipping\Configuration\CheckoutConfiguration;
use InPost\Shipping\DataProvider\PointDataProvider;
use InPost\Shipping\Traits\ErrorsTrait;
use InPost\Shipping\Traits\PhoneValidatorTrait;
use InPostCartChoiceModel;
use InPostShipping;
use Validate;

class CartChoiceUpdater
{
    use ErrorsTrait;
    use PhoneValidatorTrait;

    const TRANSLATION_SOURCE = 'CartChoiceUpdater';

    protected $module;
    protected $pointDataProvider;
    protected $configuration;

    protected $weekendDelivery = false;
    protected $cashOnDelivery = false;
    protected $service;

    /** @var Cart */
    protected $cart;
    /** @var InPostCartChoiceModel */
    protected $cartChoice;

    public function __construct(
        InPostShipping $module,
        PointDataProvider $pointDataProvider,
        CheckoutConfiguration $configuration
    ) {
        $this->module = $module;
        $this->pointDataProvider = $pointDataProvider;
        $this->configuration = $configuration;
    }

    public function getCartChoice()
    {
        return $this->cartChoice;
    }

    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
        $this->cartChoice = new InPostCartChoiceModel($cart->id);

        return $this;
    }

    public function setCarrierData(array $carrierData)
    {
        $this->weekendDelivery = $carrierData['weekendDelivery'];
        $this->cashOnDelivery = $carrierData['cashOnDelivery'];
        $this->service = $carrierData['service'];

        return $this;
    }

    public function setTargetPoint($pointId)
    {
        if (null === $pointId && $this->cartChoice->point) {
            return $this;
        }

        if ($pointId && $point = $this->pointDataProvider->getPointData($pointId)) {
            if (
                (!$this->weekendDelivery || $point->location_247) &&
                (!$this->cashOnDelivery || $point->payment_available)
            ) {
                $this->cartChoice->point = $point->getId();
            } else {
                $this->errors['locker'] = $this->module->l('Selected locker is not available for the selected delivery option.', self::TRANSLATION_SOURCE);
            }
        } else {
            $this->errors['locker'] = $this->module->l('Please select a locker.', self::TRANSLATION_SOURCE);
        }

        return $this;
    }

    public function setEmail($email)
    {
        if (!$this->configuration->getShowInputEmail()) {
            $this->cartChoice->email = null;

            return $this;
        }

        if (null === $email && $this->cartChoice->email) {
            return $this;
        }

        $email = trim($email);

        if ('' !== $email && !Validate::isEmail($email)) {
            $this->errors['email'] = $this->module->l('Provided email is invalid.', self::TRANSLATION_SOURCE);
        } else {
            $this->cartChoice->email = $email;
        }

        return $this;
    }

    public function setPhone($phone)
    {
        if (null === $phone && $this->cartChoice->phone) {
            return $this;
        }

        if ($phone) {
            $phoneToCheck = $phone;
        } else {
            $deliveryAddress = new Address($this->cart->id_address_delivery);
            $phoneToCheck = $deliveryAddress->phone_mobile ?: $deliveryAddress->phone;
        }

        if (!$this->validatePhone($phoneToCheck)) {
            $this->errors['phone'] = $this->module->l('Provided phone number is invalid - should look like XXXXXXXXX (e.g. 123456789).', self::TRANSLATION_SOURCE);
        } elseif ($this->configuration->getShowInputPhone()) {
            $this->cartChoice->phone = $phone;
        } else {
            $this->cartChoice->phone = '';
        }

        return $this;
    }

    public function saveChoice()
    {
        $this->cartChoice->service = $this->service;

        if (!Validate::isLoadedObject($this->cartChoice)) {
            $this->cartChoice->id = $this->cart->id;
            $this->cartChoice->add();
        } else {
            $this->cartChoice->update();
        }

        return $this;
    }
}
