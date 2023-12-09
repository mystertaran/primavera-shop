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

use Exception;
use InPost\Shipping\PrestaShopContext;
use Module;
use Tools;
use Translate;

class TranslateAdapter
{
    protected $shopContext;

    public function __construct(PrestaShopContext $shopContext)
    {
        $this->shopContext = $shopContext;
    }

    /**
     * Adapter for getting module translation for a specific locale on PS 1.6
     *
     * @param Module|string $module Module instance or name
     * @param string $originalString string to translate
     * @param string $source source of the original string
     * @param string|null $iso locale or language ISO code
     *
     * @return string
     *
     * @throws Exception
     */
    public function getModuleTranslation(
        $module,
        $originalString,
        $source,
        $iso = null
    ) {
        if ($this->shopContext->is17()) {
            return Translate::getModuleTranslation($module, $originalString, $source, null, false, $iso, false);
        } elseif (empty($iso)) {
            return Translate::getModuleTranslation($module, $originalString, $source);
        }

        static $translations;
        static $langCache = [];
        static $translationsMerged = [];

        $name = $module instanceof Module ? $module->name : $module;

        if (!isset($translationsMerged[$name][$iso])) {
            $filesByPriority = [
                // PrestaShop 1.5 translations
                _PS_MODULE_DIR_ . $name . '/translations/' . $iso . '.php',
                // PrestaShop 1.4 translations
                _PS_MODULE_DIR_ . $name . '/' . $iso . '.php',
                // Translations in theme
                _PS_THEME_DIR_ . 'modules/' . $name . '/translations/' . $iso . '.php',
                _PS_THEME_DIR_ . 'modules/' . $name . '/' . $iso . '.php',
            ];

            foreach ($filesByPriority as $file) {
                if (file_exists($file)) {
                    $_MODULE = null;
                    include $file;

                    if (isset($_MODULE)) {
                        $translations[$iso] = isset($translations[$iso])
                            ? array_merge($translations[$iso], $_MODULE)
                            : $_MODULE;
                    }
                }
            }

            $translationsMerged[$name][$iso] = true;
        }

        $string = preg_replace("/\\\*'/", "\'", $originalString);
        $key = md5($string);

        $cacheKey = $name . '|' . $string . '|' . $source . '|' . $iso;
        if (!isset($langCache[$cacheKey])) {
            if (!isset($translations[$iso])) {
                return str_replace('"', '&quot;', $string);
            }

            $currentKey = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $source) . '_' . $key;
            $defaultKey = Tools::strtolower('<{' . $name . '}prestashop>' . $source) . '_' . $key;

            if ('controller' == Tools::substr($source, -10, 10)) {
                $file = Tools::substr($source, 0, -10);
                $currentKeyFile = Tools::strtolower('<{' . $name . '}' . _THEME_NAME_ . '>' . $file) . '_' . $key;
                $defaultKeyFile = Tools::strtolower('<{' . $name . '}prestashop>' . $file) . '_' . $key;
            }

            if (isset($currentKeyFile) && !empty($translations[$iso][$currentKeyFile])) {
                $ret = Tools::stripslashes($translations[$iso][$currentKeyFile]);
            } elseif (isset($defaultKeyFile) && !empty($translations[$iso][$defaultKeyFile])) {
                $ret = Tools::stripslashes($translations[$iso][$defaultKeyFile]);
            } elseif (!empty($translations[$iso][$currentKey])) {
                $ret = Tools::stripslashes($translations[$iso][$currentKey]);
            } elseif (!empty($translations[$iso][$defaultKey])) {
                $ret = Tools::stripslashes($translations[$iso][$defaultKey]);
            } else {
                $ret = Tools::stripslashes($string);
            }

            $langCache[$cacheKey] = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');
        }

        return $langCache[$cacheKey];
    }
}
