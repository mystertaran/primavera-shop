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

class SzybkieZwrotyConfiguration extends ResettableConfiguration
{
    const STORE_NAME = 'INPOST_SHIPPING_SZYBKIE_ZWROTY_STORE_NAME';

    public function getStoreName()
    {
        return (string) $this->get(self::STORE_NAME);
    }

    public function setStoreName($storeName)
    {
        return $this->set(self::STORE_NAME, $storeName);
    }

    public function getUrlTemplate()
    {
        return 'https://szybkiezwroty.pl/%s#navigate-buttons';
    }

    public function getOrderReturnFormUrl($noStore = false)
    {
        if (($storeName = $this->getStoreName()) || $noStore) {
            return sprintf($this->getUrlTemplate(), $storeName);
        }

        return '';
    }
}
