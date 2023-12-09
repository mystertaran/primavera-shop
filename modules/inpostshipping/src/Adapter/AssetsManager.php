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

namespace InPost\Shipping\Adapter;

use Context;
use FrontController;
use InPost\Shipping\PrestaShopContext;
use InPostShipping;
use Validate;

class AssetsManager
{
    const GEO_WIDGET_V4_JS_URL = 'https://geowidget.easypack24.net/js/sdk-for-javascript.js';
    const GEO_WIDGET_V4_CSS_URL = 'https://geowidget.easypack24.net/css/easypack.css';

    const GEO_WIDGET_V5_URL = 'https://geowidget.inpost.pl';
    const SANDBOX_GEO_WIDGET_V5_URL = 'https://sandbox-easy-geowidget-sdk.easypack24.net';

    protected $module;
    protected $shopContext;
    protected $controller;

    public function __construct(
        InPostShipping $module,
        PrestaShopContext $shopContext
    ) {
        $this->module = $module;
        $this->shopContext = $shopContext;
        $this->controller = Context::getContext()->controller;
    }

    public function registerGeoWidgetAssets($sandbox = false)
    {
        return $this
            ->registerJavaScripts([$this->getGeoWidgetJsUrl($sandbox)], [
                'position' => 'head',
                'attributes' => 'defer',
            ])->registerStyleSheets([
                $this->getGeoWidgetCssUrl($sandbox),
            ]);
    }

    public function getGeoWidgetJsUrl($sandbox = false)
    {
        return sprintf('%s/%s', $this->getGeoWidgetUrl($sandbox), 'inpost-geowidget.js');
    }

    public function getGeoWidgetCssUrl($sandbox = false)
    {
        return sprintf('%s/%s', $this->getGeoWidgetUrl($sandbox), 'inpost-geowidget.css');
    }

    protected function getGeoWidgetUrl($sandbox)
    {
        return $sandbox
            ? self::SANDBOX_GEO_WIDGET_V5_URL
            : self::GEO_WIDGET_V5_URL;
    }

    public function registerJavaScripts(array $javaScripts, array $params = [])
    {
        $uris = array_map([$this, 'getJavaScriptUri'], $javaScripts);

        if ($this->controller instanceof FrontController && $this->shopContext->is17()) {
            $params['server'] = 'remote';

            foreach ($uris as $uri) {
                $this->controller->registerJavascript(
                    $this->getMediaId($uri),
                    $uri,
                    $params
                );
            }
        } else {
            $this->controller->addJS($uris, false);
        }

        return $this;
    }

    public function registerStyleSheets(array $styleSheets, array $params = [])
    {
        $uris = array_map([$this, 'getStyleSheetUri'], $styleSheets);

        if ($this->controller instanceof FrontController && $this->shopContext->is17()) {
            $params['server'] = 'remote';

            foreach ($uris as $uri) {
                $this->controller->registerStylesheet(
                    $this->getMediaId($uri),
                    $uri,
                    $params
                );
            }
        } else {
            $this->controller->addCSS(
                $uris,
                'all',
                null,
                false
            );
        }

        return $this;
    }

    protected function getStyleSheetUri($path)
    {
        return $this->isModuleMedia($path)
            ? $this->getModuleMediaUri('views/css/' . $path)
            : $path;
    }

    protected function getJavaScriptUri($path)
    {
        return $this->isModuleMedia($path)
            ? $this->getModuleMediaUri('views/js/' . $path)
            : $path;
    }

    protected function getModuleMediaUri($path)
    {
        return $this->module->getPathUri() . $path . '?version=' . $this->module->version;
    }

    protected function getMediaId($uri)
    {
        return 'inpost-' . sha1($uri);
    }

    protected function isModuleMedia($path)
    {
        return !Validate::isAbsoluteUrl($path)
            && strpos($path, _THEME_DIR_) === false;
    }
}
