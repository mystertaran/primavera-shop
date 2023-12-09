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
use InPost\Shipping\DataProvider\PointDataProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_11_1(InPostShipping $module)
{
    $module->clearCache();

    $configuration = $module->getService(SendingConfiguration::class);
    $dataProvider = $module->getService(PointDataProvider::class);

    $formatPoint = static function ($value) use ($dataProvider) {
        if (is_array($value) || !$value) {
            return $value;
        }

        if ($point = $dataProvider->getPointData($value)) {
            return $point->toArray();
        }

        return [
            'name' => $value,
            'address' => [
                'line1' => '',
                'line2' => '',
            ],
        ];
    };

    $locker = $configuration->getDefaultLocker();
    $pop = $configuration->getDefaultPOP();

    $newLockerValue = $formatPoint($locker);
    $newPopValue = $formatPoint($pop);

    return ($newLockerValue === $locker || $configuration->setDefaultLocker($newLockerValue))
        && ($newPopValue === $pop || $configuration->setDefaultPOP($newPopValue));
}
