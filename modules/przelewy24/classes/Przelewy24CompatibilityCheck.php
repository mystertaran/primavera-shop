<?php
/**
 * Class Przelewy24CompatibilityCheck
 *
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24CompatibilityCheck
 */
class Przelewy24CompatibilityCheck
{
    public static function checkForOrderConfirmation($suffix)
    {
        if (Configuration::get('P24_SKIP_CONFIRMATION_ENABLE')) {
            return false;
        }
        if (Configuration::get('P24_EXTRA_CHARGE_ENABLED' . $suffix)) {
            return false;
        }
        if (Configuration::get('P24_VERIFYORDER' . $suffix)) {
            return false;
        }

        return true;
    }
}
