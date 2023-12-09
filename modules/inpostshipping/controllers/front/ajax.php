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

use InPost\Shipping\CartChoiceUpdater;

class InPostShippingAjaxModuleFrontController extends ModuleFrontController
{
    const TRANSLATION_SOURCE = 'ajax';

    /** @var InPostShipping */
    public $module;

    protected $response = [
        'success' => true,
    ];

    public function postProcess()
    {
        if (!Validate::isLoadedObject($this->context->cart)) {
            $this->errors[] = $this->module->l('Shopping cart does not exist', self::TRANSLATION_SOURCE);
        } elseif (!$carrierData = $this->getCarrierData()) {
            $this->errors[] = $this->module->l('Selected carrier is not InPost Parcel Lockers', self::TRANSLATION_SOURCE);
        } else {
            switch (Tools::getValue('action')) {
                case 'updateTargetLocker':
                    $this->ajaxProcessUpdateTargetPoint($carrierData);
                    break;
                case 'updateReceiverDetails':
                    $this->ajaxProcessUpdateReceiverDetails($carrierData);
                    break;
                case 'updateChoice':
                    $this->ajaxProcessUpdateChoice($carrierData);
                    break;
            }
        }

        $this->ajaxResponse();
    }

    protected function ajaxProcessUpdateTargetPoint(array $carrierData)
    {
        $updater = $this->getUpdater($carrierData)
            ->setTargetPoint($this->getLockerFromPost($carrierData['id_carrier']))
            ->saveChoice();

        if ($updater->hasErrors()) {
            $this->errors = $updater->getErrors();
        }
    }

    protected function ajaxProcessUpdateReceiverDetails(array $carrierData)
    {
        $updater = $this->getUpdater($carrierData);
        if (Tools::getIsset('inpost_email')) {
            $updater->setEmail(Tools::getValue('inpost_email'));
        }
        if (Tools::getIsset('inpost_phone')) {
            $updater->setPhone(Tools::getValue('inpost_phone'));
        }

        $updater->saveChoice();

        if ($updater->hasErrors()) {
            $this->errors = $updater->getErrors();
        }
    }

    protected function ajaxProcessUpdateChoice(array $carrierData)
    {
        $updater = $this->getUpdater($carrierData)
            ->setEmail(Tools::getIsset('inpost_email') ? Tools::getValue('inpost_email') : null)
            ->setPhone(Tools::getIsset('inpost_phone') ? Tools::getValue('inpost_phone') : null);

        if ($carrierData['lockerService']) {
            $updater->setTargetPoint($this->getLockerFromPost($carrierData['id_carrier']));
        }

        $updater->saveChoice();

        if ($updater->hasErrors()) {
            $this->errors = $updater->getErrors();
        }
    }

    protected function getCarrierData()
    {
        $deliveryOption = $this->context->cart->getDeliveryOption();

        $carrierIds = explode(',', trim($deliveryOption[$this->context->cart->id_address_delivery], ','));
        foreach ($carrierIds as $carrierId) {
            if ($carrierData = InPostCarrierModel::getDataByCarrierId($carrierId)) {
                return $carrierData;
            }
        }

        return null;
    }

    protected function getUpdater(array $carrierData)
    {
        /** @var CartChoiceUpdater $updater */
        $updater = $this->module->getService('inpost.shipping.updater.cart_choice');

        return $updater
            ->setCart($this->context->cart)
            ->setCarrierData($carrierData);
    }

    protected function getLockerFromPost($id_carrier)
    {
        $locker = Tools::getValue('inpost_locker');

        return isset($locker[$id_carrier])
            ? $locker[$id_carrier]
            : null;
    }

    protected function ajaxResponse()
    {
        if (!empty($this->errors)) {
            $this->response = [
                'success' => false,
                'errors' => $this->errors,
            ];
        }

        header('Content-type: application/json');
        $this->ajaxDie(json_encode($this->response));
    }
}
