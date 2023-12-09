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

use Carrier;
use InPostShipping;
use PrestaShopCollection;

class Carriers implements InstallerInterface
{
    /**
     * @var InPostShipping
     */
    protected $module;

    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function install()
    {
        return true;
    }

    public function uninstall()
    {
        $result = true;

        $collection = (new PrestaShopCollection(Carrier::class))
            ->where('is_module', '=', true)
            ->where('deleted', '=', false)
            ->where('external_module_name', 'LIKE', $this->module->name);

        /** @var Carrier $carrier */
        foreach ($collection as $carrier) {
            $carrier->deleted = true;
            $carrier->is_module = false;
            $carrier->external_module_name = null;
            $carrier->shipping_external = false;
            $carrier->active = false;

            $result &= $carrier->update();
        }

        return $result;
    }
}
