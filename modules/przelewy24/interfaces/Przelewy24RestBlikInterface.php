<?php
/**
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Interface Przelewy24RestBlikInterface
 */
interface Przelewy24RestBlikInterface
{
    /**
     * Execute payment by BLIK code.
     *
     * @param $token
     * @param $blikCode
     *
     * @return object|bool
     */
    public function executePaymentByBlikCode($token, $blikCode);
}
