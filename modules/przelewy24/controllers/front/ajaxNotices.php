<?php
/**
 * Class przelewy24ajaxNoticesModuleFrontController
 *
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24ajaxNoticesModuleFrontController
 */
class Przelewy24ajaxNoticesModuleFrontController extends ModuleFrontController
{
    /**
     * Init content.
     */
    public function initContent()
    {
        parent::initContent();

        $response = ['status' => 0];
        if (!empty($_POST)) {
            if (!empty(Tools::getValue('card_remember'))) {
                $remember = (int) Tools::getValue('remember');
                $customerId = Context::getContext()->customer->id;
                if (!empty($customerId)) {  // remember my card
                    $result = Przelewy24CustomerSetting::initialize($customerId)
                        ->setIsCardRemembered($remember)
                        ->save();

                    $response = ['status' => (int) $result];
                }
            }
        }
        Przelewy24Helper::renderJson($response);
    }
}
