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

namespace InPost\Shipping\Adapter;

use Context;
use Currency;
use InPost\Shipping\PrestaShopContext;
use Tools;

class ToolsAdapter
{
    protected $shopContext;
    protected $context;

    public function __construct(PrestaShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
        $this->context = Context::getContext();
    }

    public function displayPrice($price, $currency = null)
    {
        if (!isset($currency)) {
            $currency = $this->context->currency;
        } elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance($currency);
        }

        if ($this->shopContext->is176()) {
            $isoCode = is_array($currency) ? $currency['iso_code'] : $currency->iso_code;

            return $this->context->getCurrentLocale()->formatPrice($price, $isoCode);
        }

        return Tools::displayPrice($price, $currency);
    }

    public function hash($value)
    {
        if ($this->shopContext->is176()) {
            return Tools::hash($value);
        }

        return Tools::encrypt($value);
    }
}
