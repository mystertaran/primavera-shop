<?php
/**
 * Class Przelewy24HookHelper
 *
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24HookHelper
 *
 * This class contain methods for hooks.
 */
class Przelewy24HookHelper
{
    /**
     * Check if store is configured.
     *
     * Some hooks cannot be run before configuration.
     *
     * @param string $suffix
     *
     * @return bool
     */
    public static function isStoreConfiguredForSuffix($suffix)
    {
        return
            (int) Configuration::get('P24_MERCHANT_ID' . $suffix) > 0
            && (int) Configuration::get('P24_SHOP_ID' . $suffix) > 0
            && !empty(Configuration::get('P24_SALT' . $suffix))
        ;
    }
}
