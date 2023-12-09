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

namespace InPost\Shipping\Presenter\Store;

class StorePresenter implements PresenterInterface
{
    /**
     * @var PresenterInterface[]
     */
    protected $modules;

    /**
     * @param PresenterInterface[] $modules
     */
    public function __construct(array $modules)
    {
        $this->modules = array_filter($modules, function ($module) {
            return $module instanceof PresenterInterface;
        });
    }

    public function present()
    {
        $store = [];

        foreach ($this->modules as $module) {
            $store = array_merge($store, $module->present());
        }

        return $store;
    }
}
