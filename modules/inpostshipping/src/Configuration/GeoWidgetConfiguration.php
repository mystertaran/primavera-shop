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

class GeoWidgetConfiguration extends ResettableConfiguration
{
    const TOKEN = 'INPOST_SHIPPING_GEOWIDGET_TOKEN';
    const SANDBOX_TOKEN = 'INPOST_SHIPPING_GEOWIDGET_SANDBOX_TOKEN';
    const FO_USE_SANDBOX = 'INPOST_SHIPPING_GEOWIDGET_FO_USE_SANDBOX';

    public function getProductionToken()
    {
        return (string) $this->get(self::TOKEN);
    }

    public function setProductionToken($token)
    {
        return $this->set(self::TOKEN, $token);
    }

    public function getSandboxToken()
    {
        return (string) $this->get(self::SANDBOX_TOKEN);
    }

    public function setSandboxToken($token)
    {
        return $this->set(self::SANDBOX_TOKEN, $token);
    }

    public function shouldUseSandboxOnTheFrontOffice()
    {
        return (bool) $this->get(self::FO_USE_SANDBOX);
    }

    public function setUseSandboxOnTheFrontOffice($useSandbox)
    {
        return $this->set(self::FO_USE_SANDBOX, (bool) $useSandbox);
    }
}
