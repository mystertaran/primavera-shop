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

use InPost\Shipping\Install\Database;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_11_0(InPostShipping $module)
{
    $database = $module->getService(Database::class);
    if (!$database->columnExists('inpost_carrier', 'zabka')) {
        return true;
    }

    $query = (new DbQuery())
        ->select('c.*, cl.*')
        ->from('inpost_carrier', 'ic')
        ->innerJoin('carrier', 'c', 'c.id_reference = ic.id_reference')
        ->leftJoin('carrier_lang', 'cl', 'cl.id_carrier = c.id_carrier')
        ->where('ic.zabka = 1')
        ->where('c.deleted = 0');

    $result = true;

    if ($data = Db::getInstance()->executeS($query)) {
        $carriers = Carrier::hydrateCollection(Carrier::class, $data);

        /** @var Carrier $carrier */
        foreach ($carriers as $carrier) {
            $carrier->deleted = true;
            $carrier->setFieldsToUpdate([
                'deleted' => true,
            ]);
            $result &= $carrier->update();
        }
    }

    return $result && $database->upgradeSchema();
}
