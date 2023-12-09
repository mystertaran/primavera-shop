<?php
/**
 * Class Przelewy24paymentFinishedModuleFrontController
 *
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24paymentFinishedModuleFrontController
 */
class Przelewy24paymentFinishedModuleFrontController extends ModuleFrontController
{
    const TIME_TO_WAIT_FOR_PAYMENT_STATUS = 2;
    const MAXIMUM_TRY = 3;
    const FETCH_LAST_ORDER = false;

    /**
     * Redirect to finished or failed payment.
     */
    public function initContent()
    {
        parent::initContent();
        $order = null;
        $idOrder = Tools::getValue('id_order');
        $idCart = Tools::getValue('id_cart');
        $sleep = Tools::getValue('sleep') ? Tools::getValue('sleep') : 0;

        sleep(self::TIME_TO_WAIT_FOR_PAYMENT_STATUS); // wait for payment status

        if ($idOrder) {
            $order = new Order($idOrder);
        } elseif ($idCart && ($cardOrderId = Order::getIdByCartId($idCart))) {
            $order = new Order($cardOrderId);
        }

        if (!$order && self::FETCH_LAST_ORDER) {
            // last created order
            $sql = new DbQuery();
            $sql->select('max(id_order) as id');
            $sql->from('orders');
            $sql->where('id_customer = \'' . pSQL(Context::getContext()->customer->id) . '\'');

            $lastInsert = Db::getInstance()->getRow($sql->build());

            if (is_array($lastInsert)) {
                $order = new Order($lastInsert['id']);
            }
        }

        if ($order && ((int) $order->current_state === (int) Configuration::get('P24_ORDER_STATE_2'))) {
            Tools::redirect(
                $this->context->link->getModuleLink(
                    'przelewy24',
                    'paymentSuccessful',
                    [],
                    '1' === (string) Configuration::get('PS_SSL_ENABLED')
                )
            );
        } else {
            if (($sleep < self::MAXIMUM_TRY)
                && !($order && ((int) $order->current_state === (int) Configuration::get('PS_OS_ERROR')))) {
                // paymentStatus not yet processed
                ++$sleep;

                $returnParamArray = ['sleep' => $sleep];
                if ($idOrder) {
                    $returnParamArray['id_order'] = $idOrder;
                } elseif ($idCart) {
                    $returnParamArray['id_cart'] = $idCart;
                }

                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'przelewy24',
                        'paymentFinished',
                        $returnParamArray,
                        '1' === (string) Configuration::get('PS_SSL_ENABLED')
                    )
                );
            } else {
                Tools::redirect(
                    $this->context->link->getModuleLink(
                        'przelewy24',
                        'paymentFailed',
                        $order ? ['id_order' => $order->id] : [],
                        '1' === (string) Configuration::get('PS_SSL_ENABLED')
                    )
                );
            }
        }
    }
}
