<?php
/**
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Interface Przelewy24RestCardInterface
 */
interface Przelewy24RestCardInterface
{
    /**
     * Charge with3ds.
     *
     * @param $token
     *
     * @return array
     */
    public function chargeWith3ds($token);
}
