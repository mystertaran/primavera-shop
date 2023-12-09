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

namespace InPost\Shipping\Handler\DeliveryOption;

use Cart;
use Currency;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;
use InPost\Shipping\TimeChecker;
use InPostCarrierModel;
use Tools;

class CheckDeliveryOptionHandler
{
    protected $timeChecker;
    protected $tokenProvider;

    public function __construct(
        TimeChecker $timeChecker,
        GeoWidgetTokenProvider $tokenProvider
    ) {
        $this->timeChecker = $timeChecker;
        $this->tokenProvider = $tokenProvider;
    }

    public function check(Cart $cart, $id_carrier)
    {
        $carrierData = InPostCarrierModel::getDataByCarrierId($id_carrier);

        if ($carrierData['cashOnDelivery'] && $idCurrency = Currency::getIdByIsoCode('PLN')) {
            $products = $cart->getProducts();
            $currencyFrom = Currency::getCurrencyInstance((int) $cart->id_currency);
            $currencyTo = Currency::getCurrencyInstance((int) $idCurrency);
            foreach ($products as $product) {
                $pricePLN = Tools::convertPriceFull($product['price_wt'], $currencyFrom, $currencyTo);
                if ($pricePLN >= 5000) {
                    return false;
                }
            }
        }

        if ($carrierData['lockerService']) {
            if (empty($this->tokenProvider->getToken())) {
                return false;
            }

            return !$carrierData['weekendDelivery']
                || $this->timeChecker->shouldEnableWeekendDelivery();
        }

        return true;
    }
}
