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

use InPost\Shipping\Hook\AbstractHook;
use InPost\Shipping\Hook\AdminOrderDetails;
use InPost\Shipping\Hook\AdminOrderList;
use InPost\Shipping\Hook\AdminProduct;
use InPost\Shipping\Hook\Assets;
use InPost\Shipping\Hook\Checkout;
use InPost\Shipping\Hook\Mail;
use InPost\Shipping\Hook\OrderConfirmation;
use InPost\Shipping\Hook\OrderHistory;
use InPostShipping;

class HookDispatcher
{
    const HOOK_CLASSES = [
        AdminOrderDetails::class,
        AdminOrderList::class,
        AdminProduct::class,
        Assets::class,
        Checkout::class,
        Mail::class,
        OrderHistory::class,
        OrderConfirmation::class,
    ];

    /**
     * Hook instances.
     *
     * @var AbstractHook[]
     */
    protected $hooks = [];

    public function __construct(InPostShipping $module, PrestaShopContext $shopContext)
    {
        foreach (static::HOOK_CLASSES as $hookClass) {
            /** @var AbstractHook $hook */
            $hook = new $hookClass($module, $shopContext);
            $this->hooks[] = $hook;
        }
    }

    /**
     * Get available hooks
     *
     * @return string[]
     */
    public function getAvailableHooks()
    {
        $availableHooks = [];
        foreach ($this->hooks as $hook) {
            $availableHooks[] = $hook->getAvailableHooks();
        }

        return array_merge(...$availableHooks);
    }

    public function dispatch($hookName, array $params = [])
    {
        foreach ($this->hooks as $hook) {
            if (method_exists($hook, $hookName)) {
                return $hook->{$hookName}($params);
            }
        }

        return false;
    }

    public function getOutdatedHooks()
    {
        $outdatedHooks = [];
        foreach ($this->hooks as $hook) {
            $outdatedHooks[] = $hook->getOutdatedHooks();
        }

        return array_merge(...$outdatedHooks);
    }
}
