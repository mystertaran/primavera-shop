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

use InPost\Shipping\Configuration\ShopConfiguration;
use InPost\Shipping\Install\Hooks;

class HookUpdater
{
    protected $configuration;
    protected $hookInstaller;

    public function __construct(
        ShopConfiguration $configuration,
        Hooks $hookInstaller
    ) {
        $this->configuration = $configuration;
        $this->hookInstaller = $hookInstaller;
    }

    public function updateHookRegistrations()
    {
        // register new hooks after PS version upgrade
        if (version_compare(_PS_VERSION_, $this->configuration->getPrestashopVersion(), '<=')) {
            return true;
        }

        $this->hookInstaller->uninstallOutdated();

        return $this->hookInstaller->install()
            && $this->configuration->updatePrestashopVersion();
    }
}
