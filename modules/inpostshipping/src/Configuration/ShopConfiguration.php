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

class ShopConfiguration extends AbstractConfiguration
{
    const PS_VERSION = 'INPOST_SHIPPING_PS_VERSION';
    const DB_SCHEMA_VERSION = 'INPOST_SHIPPING_DB_SCHEMA_VERSION';

    public function getPrestashopVersion()
    {
        return $this->getGlobal(self::PS_VERSION);
    }

    public function updatePrestashopVersion()
    {
        return $this->setGlobal(self::PS_VERSION, _PS_VERSION_);
    }

    public function getDatabaseSchemaVersion()
    {
        return $this->getGlobal(self::DB_SCHEMA_VERSION);
    }

    public function updateDatabaseSchemaVersion($version)
    {
        return $this->setGlobal(self::DB_SCHEMA_VERSION, $version);
    }

    public function getPriceDisplayPrecision()
    {
        return (int) $this->get('PS_PRICE_DISPLAY_PRECISION');
    }

    public function setDefaults()
    {
        return $this->updatePrestashopVersion();
    }
}
