<?php
/**
 * @author Przelewy24
 * @copyright Przelewy24
 * @license https://www.gnu.org/licenses/lgpl-3.0.en.html
 */

/**
 * Class Przelewy24chargeCardExtModuleFrontController
 */
class Przelewy24chargeCardExtModuleFrontController extends ModuleFrontController
{
    const E_DATE = 'wrong-date';

    /**
     * Init contant and dispatch actions.
     *
     * @throws Exception
     */
    public function initContent()
    {
        parent::initContent();

        $actionName = Tools::getValue('action');
        switch ($actionName) {
            case 'executeCardParams':
                $this->executeCardParams();
                break;
            default:
                $this->ajaxRender('', self::class, 'unknown');
                exit;
        }
    }

    /**
     * Execute a card payment with params.
     *
     * @return void
     */
    private function executeCardParams()
    {
        $success = false;
        $returnUrl = '/';
        $reload = true;

        $cart = $this->tryGetCartFromId();
        if (!$cart) {
            $cart = Context::getContext()->cart;
        }

        $params = $this->getValidParams($errors);
        if ($errors) {
            $reload = false;
        } elseif ($cart && $cart->id) {
            $przelewy24 = new Przelewy24();
            $paymentData = new Przelewy24PaymentData($cart);

            $commonHelper = new Przelewy24Common($przelewy24);
            $commonHelper->validateOrderIfNot1($paymentData);

            $method = $this->getValidMethod();
            $token = $this->registerCardTransaction($paymentData, $method);
            if ($token) {
                $currencySuffix = $commonHelper->getSuffix($paymentData);
                $restCard = Przelewy24RestCardFactory::buildForSuffix($currencySuffix);
                $response = $restCard->chargeWithParams($token, $params);

                if (isset($response['data']['orderId']) && $response['data']['orderId']) {
                    $this->context->cookie->id_cart = null;
                    $success = true;
                }
            }

            if ($success) {
                if (isset($response['data']['redirectUrl']) && $response['data']['redirectUrl']) {
                    $returnUrl = $response['data']['redirectUrl'];
                } else {
                    $returnUrl = Przelewy24TransactionSupport::generateReturnUrl($paymentData);
                }
            } elseif ($paymentData->orderExists()) {
                $returnUrl = Przelewy24TransactionSupport::generateReturnUrl($paymentData);
            } else {
                $reload = false;
            }
        }

        $data = [
            'success' => $success,
            'returnUrl' => $returnUrl,
            'reload' => $reload,
        ];

        $this->ajaxRender(json_encode($data), self::class, 'executeBlik');
        exit;
    }

    /**
     * Register blik transaction.
     *
     * @return string
     */
    private function registerCardTransaction(Przelewy24PaymentData $paymentData, int $method)
    {
        $currency = $paymentData->getCurrency();
        $suffix = ('PLN' === $currency->iso_code) ? '' : '_' . $currency->iso_code;
        $description = Przelewy24OrderDescriptionHelper::buildDescriptionConfigured(
            $this->module->l('Order'),
            $this->module->l('Cart'),
            $suffix,
            $paymentData
        );
        $transactionSupport = new Przelewy24TransactionSupport();
        $languageIsoCode = $this->context->language->iso_code;

        return $transactionSupport->registerTransaction($paymentData, $description, $languageIsoCode, true, $method);
    }

    /**
     * Get cart based on id in post.
     *
     * @return Cart|null
     */
    private function tryGetCartFromId()
    {
        if (!Tools::getValue('cartId')) {
            return null;
        }

        $cartId = (int) Tools::getValue('cartId');
        $cart = new Cart($cartId);
        if (!$cart->id) {
            return null;
        }

        $customer = $this->context->customer;
        if (!Przelewy24Tools::checkCartForCustomer($customer, $cart)) {
            return null;
        }

        return $cart;
    }

    private function getValidMethod()
    {
        $method = (int) Tools::getValue('method');
        $validMethods = Przelewy24OneClickHelper::getCardPaymentIds();
        if (in_array($method, $validMethods)) {
            return $method;
        } else {
            return 0;
        }
    }

    /**
     * Get valid Blik code or null.
     *
     * @return Przelewy24PureCardParams
     */
    private function getValidParams(&$errors)
    {
        $errors = [];

        $ret = new Przelewy24PureCardParams();
        $ret->holder = Tools::getValue('holder');
        $ret->number = Tools::getValue('number');
        try {
            $dateY = Tools::getValue('date_y');
            $dateM = Tools::getValue('date_m');
            $ret->date = new DateTime($dateY . '-' . $dateM . '-01');
        } catch (Exception $ex) {
            $errors[] = self::E_DATE;
        }
        $ret->cvv = Tools::getValue('cvv_nr');

        return $ret;
    }
}
