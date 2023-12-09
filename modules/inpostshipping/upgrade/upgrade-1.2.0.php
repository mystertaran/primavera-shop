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

use InPost\Shipping\Configuration\SendingConfiguration;
use InPost\Shipping\Install\Database;
use InPost\Shipping\Install\Hooks;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_2_0(InPostShipping $module)
{
    $module->clearCache();

    $configuration = $module->getService(SendingConfiguration::class);
    $database = $module->getService(Database::class);
    $hooks = $module->getService(Hooks::class);

    return $configuration->setDefaults()
        && $database->createTables()
        && $hooks->install()
        && $database->upgradeSchema();
}
