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

namespace InPost\Shipping\Install;

use InPost\Shipping\HookDispatcher;
use InPostShipping;

class Hooks implements InstallerInterface
{
    protected $module;
    protected $hookDispatcher;

    public function __construct(InPostShipping $module, HookDispatcher $hookDispatcher)
    {
        $this->module = $module;
        $this->hookDispatcher = $hookDispatcher;
    }

    public function install()
    {
        return $this->module->registerHook(
            $this->hookDispatcher->getAvailableHooks()
        );
    }

    public function uninstall()
    {
        foreach ($this->hookDispatcher->getAvailableHooks() as $hookName) {
            $this->module->unregisterHook($hookName);
        }

        return true;
    }

    public function uninstallOutdated()
    {
        foreach ($this->hookDispatcher->getOutdatedHooks() as $hookName) {
            $this->module->unregisterHook($hookName);
        }
    }
}
