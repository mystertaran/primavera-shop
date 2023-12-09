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

use Address;
use Carrier;
use Cart;
use Customer;
use InPost\Shipping\CartChoiceUpdater;
use InPost\Shipping\Configuration\CheckoutConfiguration;
use InPost\Shipping\Presenter\CheckoutDataPresenter;
use InPost\Shipping\Traits\ErrorsTrait;
use InPostCarrierModel;
use InPostCartChoiceModel;
use ModuleFrontController;
use Order;
use OrderController;
use Tools;

class Checkout extends AbstractHook
{
    use ErrorsTrait;

    const HOOK_LIST = [
        'actionCarrierProcess',
        'actionValidateOrder',
    ];

    const HOOK_LIST_16 = [
        'displayCarrierList',
    ];

    const HOOK_LIST_17 = [
        'displayCarrierExtraContent',
        'actionValidateStepComplete',
    ];

    protected $deliveryChoiceProcessed = false;

    public function hookActionCarrierProcess(array $params)
    {
        if ($this->shopContext->is17()) {
            if (!$this->deliveryChoiceProcessed && Tools::isSubmit('delivery_option')) {
                $this->processDeliveryChoice(
                    $params['cart'],
                    Tools::getAllValues(),
                    $this->context->controller instanceof ModuleFrontController
                );
            }
        } elseif (Tools::getValue('step') == OrderController::STEP_PAYMENT) {
            $this->processDeliveryChoice($params['cart'], Tools::getAllValues());

            if ($this->hasErrors()) {
                $this->module->getAssetsManager()
                    ->registerJavaScripts([
                        _THEME_JS_DIR_ . 'order-carrier.js',
                    ]);
            }
        }
    }

    public function hookActionValidateStepComplete(array $params)
    {
        if ('delivery' !== $params['step_name']) {
            return;
        }

        $deliveryChoiceProcessed = $this->processDeliveryChoice(
            $this->context->cart,
            $params['request_params'],
            $this->context->controller instanceof ModuleFrontController
        );

        if (!$deliveryChoiceProcessed) {
            return;
        }

        $this->deliveryChoiceProcessed = true;
        if (!$errors = $this->getErrors()) {
            return;
        }

        $params['completed'] = false;
        $this->storeSessionData($params['request_params']);

        if (
            isset($errors['phone'])
            && !$this->module->getService(CheckoutConfiguration::class)->getShowInputPhone()
        ) {
            $this->context->controller->errors = array_merge(
                $this->context->controller->errors,
                array_values($errors)
            );
        }
    }

    protected function processDeliveryChoice(Cart $cart, array $requestParams, $addControllerErrors = true)
    {
        if (!$carriersList = $this->getCarriersList($cart, $requestParams)) {
            return false;
        }

        $carrierIds = explode(',', trim($carriersList, ','));
        foreach ($carrierIds as $carrierId) {
            if (!$carrierData = InPostCarrierModel::getDataByCarrierId($carrierId)) {
                continue;
            }

            $updater = $this->getCartChoiceUpdater($cart, $carrierData)
                ->setEmail(isset($requestParams['inpost_email']) ? $requestParams['inpost_email'] : null)
                ->setPhone(isset($requestParams['inpost_phone']) ? $requestParams['inpost_phone'] : null);

            if ($carrierData['lockerService']) {
                $locker = isset($requestParams['inpost_locker'][$carrierId])
                    ? $requestParams['inpost_locker'][$carrierId]
                    : null;

                $updater->setTargetPoint($locker);
            }

            $updater->saveChoice();

            if ($updater->hasErrors()) {
                $this->setErrors($errors = $updater->getErrors());

                if ($addControllerErrors) {
                    $this->context->controller->errors = array_merge($this->context->controller->errors, array_values($errors));
                }
            }

            return true;
        }

        return false;
    }

    protected function getCartChoiceUpdater(Cart $cart, array $carrierData)
    {
        /** @var CartChoiceUpdater $updater */
        $updater = $this->module->getService('inpost.shipping.updater.cart_choice');

        return $updater
            ->setCart($cart)
            ->setCarrierData($carrierData);
    }

    public function hookDisplayCarrierExtraContent(array $params)
    {
        if (!$this->deliveryChoiceProcessed && Tools::getValue('confirmDeliveryOption')) {
            return '';
        }

        if (!$carrierData = InPostCarrierModel::getDataByCarrierId($params['carrier']['id'])) {
            return '';
        }

        $this->assignTemplateVariables($carrierData, $this->retrieveSessionData());

        return $this->module->display(
            $this->module->name,
            'views/templates/hook/carrier-extra-content.tpl'
        );
    }

    public function hookDisplayCarrierList(array $params)
    {
        $content = '';

        /** @var Address $address */
        $address = $params['address'];
        $deliveryOption = $this->context->cart->getDeliveryOption(null, true);

        $carrierIds = explode(',', trim($deliveryOption[$address->id], ','));
        foreach ($carrierIds as $carrierId) {
            if ($carrierData = InPostCarrierModel::getDataByCarrierId($carrierId)) {
                $this->assignTemplateVariables($carrierData);

                $content .= $this->module->display(
                    $this->module->name,
                    'views/templates/hook/16/carrier-extra-content.tpl'
                );
            }
        }

        return $content;
    }

    protected function assignTemplateVariables(array $carrierData, array $sessionData = [])
    {
        /** @var CheckoutDataPresenter $presenter */
        $presenter = $this->module->getService('inpost.shipping.presenter.checkout_data');
        $configuration = $this->module->getService(CheckoutConfiguration::class);

        $variables = $presenter->present($carrierData, $sessionData);
        $variables['showInputEmail'] = $configuration->getShowInputEmail();
        $variables['showInputPhone'] = $configuration->getShowInputPhone();
        $variables['renderHiddenPhoneInput'] = !$variables['showInputPhone'] && $this->shouldRenderHiddenPhoneInput();

        $this->context->smarty->assign($variables);
    }

    public function hookActionValidateOrder(array $params)
    {
        /** @var Order $order */
        $order = $params['order'];
        $carrier = new Carrier($order->id_carrier);
        $cartChoice = new InPostCartChoiceModel($order->id_cart);

        if ($carrier->external_module_name !== $this->module->name) {
            $cartChoice->delete();
        } elseif (empty($cartChoice->email) || empty($cartChoice->phone)) {
            $this->updateCartChoiceByOrderData($cartChoice, $order);
        }
    }

    // preserve errors and submitted values to retrieve after redirect
    protected function storeSessionData(array $requestParams)
    {
        $data = json_encode([
            'email' => isset($requestParams['inpost_email']) ? $requestParams['inpost_email'] : null,
            'phone' => isset($requestParams['inpost_phone']) ? $requestParams['inpost_phone'] : null,
            'errors' => $this->getErrors(),
        ]);

        $this->context->cookie->inpost_data = $data;
    }

    protected function retrieveSessionData()
    {
        static $data;

        if (!isset($data)) {
            $data = [];

            if (isset($this->context->cookie->inpost_data)) {
                $data = json_decode($this->context->cookie->inpost_data, true);
                unset($this->context->cookie->inpost_data);
            }
        }

        return $data;
    }

    private function updateCartChoiceByOrderData(InPostCartChoiceModel $cartChoice, Order $order)
    {
        if (empty($cartChoice->service)) {
            if (null === $data = InPostCarrierModel::getDataByCarrierId($order->id_carrier)) {
                return;
            }

            $cartChoice->service = $data['service'];
        }

        if (empty($cartChoice->phone)) {
            $address = new Address($order->id_address_delivery);
            $cartChoice->phone = $address->phone_mobile ?: $address->phone;
        }

        if (empty($cartChoice->email)) {
            $customer = new Customer($order->id_customer);
            $cartChoice->email = trim($customer->email);
        }

        if (empty($cartChoice->id)) {
            $cartChoice->id = (int) $order->id_cart;
            $cartChoice->add();
        } else {
            $cartChoice->update();
        }
    }

    private function getCarriersList(Cart $cart, array $requestParams)
    {
        $deliveryOption = isset($requestParams['delivery_option'])
            ? $requestParams['delivery_option']
            : $cart->getDeliveryOption(null, true);

        if (isset($deliveryOption[$cart->id_address_delivery])) {
            return $deliveryOption[$cart->id_address_delivery];
        }

        return isset($deliveryOption[0]) && $this->isGuestEtsOpcContext()
            ? $deliveryOption[0]
            : null;
    }

    private function shouldRenderHiddenPhoneInput()
    {
        return $this->isOpcModuleContext();
    }

    private function isOpcModuleContext()
    {
        if (!$this->context->controller instanceof ModuleFrontController) {
            return false;
        }

        return in_array($this->context->controller->module->name, [
            Assets::SUPERCHECKOUT_MODULE,
            Assets::STEASYCHECKOUT_MODULE,
            Assets::THECHECKOUT_MODULE,
        ], true);
    }

    private function isGuestEtsOpcContext()
    {
        if (!$this->context->controller instanceof ModuleFrontController || $this->context->customer->isLogged()) {
            return false;
        }

        return Assets::ETS_ONEPAGECHECKOUT_MODULE === $this->context->controller->module->name;
    }
}
