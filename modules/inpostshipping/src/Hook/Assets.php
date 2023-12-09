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

namespace InPost\Shipping\Hook;

use InPost\Shipping\Configuration\CheckoutConfiguration;
use InPost\Shipping\GeoWidget\GeoWidgetTokenProvider;
use InPost\Shipping\HookUpdater;
use InPost\Shipping\ShipX\Resource\Service;
use Media;
use Module;
use ModuleFrontController;
use OrderController;
use Tools;

class Assets extends AbstractHook
{
    const HOOK_LIST = [
        'actionAdminControllerSetMedia',
        'actionFrontControllerSetMedia',
        'displayAdminAfterHeader',
    ];

    const SUPERCHECKOUT_MODULE = 'supercheckout';
    const STEASYCHECKOUT_MODULE = 'steasycheckout';
    const THECHECKOUT_MODULE = 'thecheckout';
    const ETS_ONEPAGECHECKOUT_MODULE = 'ets_onepagecheckout';
    const WKONEPAGECHECKOUT_MODULE = 'wkonepagecheckout';

    protected $ordersDisplay;

    public function hookActionAdminControllerSetMedia()
    {
        $this->updateHookRegistrations();

        if ($this->shouldDisplayLoader()) {
            $this->module
                ->getAssetsManager()
                ->registerStyleSheets(['admin/loader.css']);
        }

        if ('AdminOrders' !== Tools::getValue('controller')) {
            return;
        }

        $display = $this->getOrdersDisplay();

        Media::addJsDef([
            'shopIs177' => $this->shopContext->is177(),
            'inPostLockerCarrierServices' => Service::LOCKER_CARRIER_SERVICES,
            'inPostLockerServices' => Service::LOCKER_SERVICES,
            'inPostLockerStandard' => Service::INPOST_LOCKER_STANDARD,
            'inPostLockerEconomy' => Service::INPOST_LOCKER_ECONOMY,
        ]);

        if ($display === 'view') {
            $assetsManager = $this->module->getAssetsManager();

            $assetsManager
                ->registerJavaScripts([
                    'admin/tools.js',
                    'admin/common.js',
                    'admin/order-details.js',
                ])
                ->registerStyleSheets([
                    'admin/orders.css',
                ]);

            $this->setGeoWidgetMedia();
        } elseif ($display === 'index') {
            $this->module
                ->getAssetsManager()
                ->registerJavaScripts([
                    'admin/tools.js',
                    'admin/order-list.js',
                ]);
        }
    }

    public function hookDisplayAdminAfterHeader()
    {
        return $this->shouldDisplayLoader()
            ? $this->module->display($this->module->name, 'views/templates/hook/loader.tpl')
            : '';
    }

    protected function shouldDisplayLoader()
    {
        return Tools::getValue('controller') === 'AdminOrders'
            && in_array($this->getOrdersDisplay(), ['index', 'view'])
            || isset($this->context->controller->module)
            && $this->context->controller->module === $this->module;
    }

    public function hookActionFrontControllerSetMedia()
    {
        if (!$this->isCheckoutControllerContext()) {
            return;
        }

        $assetsManager = $this->module->getAssetsManager();

        $assetsManager
            ->registerJavaScripts([
                $this->shopContext->is17() ? 'checkout17.js' : 'checkout16.js',
            ])
            ->registerStyleSheets([
                'front.css',
            ]);

        if ($scripts = $this->getModuleSpecificScriptFiles()) {
            $assetsManager->registerJavaScripts($scripts);
        }

        Media::addJsDef([
            'inPostAjaxController' => $this->context->link->getModuleLink($this->module->name, 'ajax'),
        ]);

        $this->setGeoWidgetMedia();
    }

    protected function getOrdersDisplay()
    {
        if (!isset($this->ordersDisplay)) {
            $this->ordersDisplay = $this->initOrdersDisplay();
        }

        return $this->ordersDisplay;
    }

    protected function initOrdersDisplay()
    {
        if ($this->shopContext->is177()) {
            switch (Tools::getValue('action')) {
                case 'vieworder':
                    return 'view';
                case 'addorder':
                    return 'create';
                default:
                    return 'index';
            }
        }

        if (Tools::isSubmit('vieworder')) {
            return 'view';
        }

        if (Tools::isSubmit('addorder')) {
            return 'create';
        }

        return 'index';
    }

    protected function isCheckoutControllerContext()
    {
        $controller = Tools::getValue('controller');

        if (in_array($controller, ['order', 'orderopc'])) {
            return true;
        }

        if (!$this->context->controller instanceof ModuleFrontController) {
            return false;
        }

        switch ($this->context->controller->module->name) {
            case self::SUPERCHECKOUT_MODULE:
                return 'supercheckout' === $this->getModuleControllerName($controller);
            case self::STEASYCHECKOUT_MODULE:
                return 'default' === $this->getModuleControllerName($controller);
            case self::THECHECKOUT_MODULE:
                return 'front' === $this->getModuleControllerName($controller);
            case self::ETS_ONEPAGECHECKOUT_MODULE:
                return 'order' === $this->getModuleControllerName($controller);
            default:
                return $this->isCustomCheckoutController(
                    $this->context->controller->module->name,
                    $controller
                );
        }
    }

    protected function getModuleControllerName($controller)
    {
        $parts = explode('-', $controller);

        return end($parts);
    }

    protected function isCustomCheckoutController($moduleName, $controller)
    {
        /** @var CheckoutConfiguration $configuration */
        $configuration = $this->module->getService('inpost.shipping.configuration.checkout');

        if (!$configuration->isUsingCustomCheckoutModule()) {
            return false;
        }

        $controllers = $configuration->getCustomCheckoutControllers();

        return isset($controllers[$moduleName])
            && in_array($this->getModuleControllerName($controller), $controllers[$moduleName], true);
    }

    protected function getModuleSpecificScriptFiles()
    {
        if ($this->context->controller instanceof ModuleFrontController) {
            switch ($this->context->controller->module->name) {
                case self::SUPERCHECKOUT_MODULE:
                    Media::addJsDef([
                        'inPostSuperCheckoutGuest' => !$this->context->customer->isLogged(),
                    ]);

                    return ['modules/supercheckout.js'];
                case self::STEASYCHECKOUT_MODULE:
                    return ['modules/steasycheckout.js'];
                case self::THECHECKOUT_MODULE:
                    return ['modules/thecheckout.js'];
                default:
                    return [];
            }
        }

        if (
            $this->context->controller instanceof OrderController &&
            Module::isEnabled(self::WKONEPAGECHECKOUT_MODULE)
        ) {
            return ['modules/wkonepagecheckout.js'];
        }

        return [];
    }

    protected function updateHookRegistrations()
    {
        if (!$this->shopContext->is17()) {
            return;
        }

        /** @var HookUpdater $updater */
        $updater = $this->module->getService('inpost.shipping.hook_updater');

        $updater->updateHookRegistrations();
    }

    protected function setGeoWidgetMedia()
    {
        /** @var GeoWidgetTokenProvider $tokenProvider */
        $tokenProvider = $this->module->getService('inpost.shipping.geo_widget.token_provider');

        if (!$token = $tokenProvider->getToken()) {
            return;
        }

        $this->module
            ->getAssetsManager()
            ->registerGeoWidgetAssets($token->isSandbox())
            ->registerJavaScripts(['geowidget_v5.js']);

        Media::addJsDef([
            'inPostGeoWidgetToken' => $token->getToken(),
            'inPostLanguage' => Tools::strtolower($this->context->language->iso_code) === 'pl' ? 'pl' : 'en',
        ]);
    }
}
