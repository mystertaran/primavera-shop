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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_9_1(InPostShipping $module)
{
    $module->clearCache();

    $query = (new DbQuery())
        ->select('c.*, cl.*')
        ->from('carrier', 'c')
        ->leftJoin('carrier_lang', 'cl', 'cl.id_carrier = c.id_carrier')
        ->leftJoin('inpost_carrier', 'ic', 'ic.id_reference = c.id_reference')
        ->where('ic.id_reference IS NULL')
        ->where('c.deleted = 0')
        ->where('c.is_module = 1')
        ->where('c.external_module_name LIKE "' . pSQL($module->name) . '"');

    $result = true;
    if ($data = Db::getInstance()->executeS($query)) {
        $carriers = Carrier::hydrateCollection(Carrier::class, $data);

        /** @var Carrier $carrier */
        foreach ($carriers as $carrier) {
            $carrier->is_module = false;
            $carrier->external_module_name = null;
            $carrier->shipping_external = false;
            $carrier->setFieldsToUpdate([
                'is_module' => true,
                'shipping_external' => true,
                'external_module_name' => true,
            ]);

            $result &= $carrier->update();
        }
    }

    return $result;
}
