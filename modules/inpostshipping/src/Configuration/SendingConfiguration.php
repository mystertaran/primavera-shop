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

namespace InPost\Shipping\Configuration;

use InPost\Shipping\ChoiceProvider\ShipmentReferenceFieldChoiceProvider;

class SendingConfiguration extends ResettableConfiguration
{
    const SENDER_DETAILS = 'INPOST_SHIPPING_SENDER_DETAILS';
    const DEFAULT_SENDING_METHOD = 'INPOST_SHIPPING_DEFAULT_SENDING_METHOD';
    const DEFAULT_LOCKER = 'INPOST_SHIPPING_DEFAULT_LOCKER';
    const DEFAULT_POP = 'INPOST_SHIPPING_DEFAULT_POP';
    const DEFAULT_DISPATCH_POINT_ID = 'INPOST_SHIPPING_DEFAULT_DISPATCH_POINT';
    const DEFAULT_SHIPMENT_REFERENCE_FIELD = 'INPOST_SHIPPING_DEFAULT_SHIPMENT_REFERENCE_FIELD';
    const DEFAULT_INSURANCE_AMOUNT = 'INPOST_SHIPPING_DEFAULT_INSURANCE_AMOUNT';

    public function getSenderDetails()
    {
        return json_decode($this->get(self::SENDER_DETAILS), true);
    }

    public function setSenderDetails(array $senderDetails)
    {
        $senderDetails = $senderDetails ? json_encode($senderDetails) : false;

        return $this->set(self::SENDER_DETAILS, $senderDetails);
    }

    public function getDefaultSendingMethod()
    {
        return (string) $this->get(self::DEFAULT_SENDING_METHOD);
    }

    public function setDefaultSendingMethod($sendingMethod)
    {
        return $this->set(self::DEFAULT_SENDING_METHOD, $sendingMethod);
    }

    public function getDefaultLocker()
    {
        return json_decode($this->get(self::DEFAULT_LOCKER), true);
    }

    public function setDefaultLocker($locker)
    {
        $locker = $locker ? json_encode($locker) : false;

        return $this->set(self::DEFAULT_LOCKER, $locker);
    }

    public function getDefaultPOP()
    {
        return json_decode($this->get(self::DEFAULT_POP), true);
    }

    public function setDefaultPOP($pop)
    {
        $pop = $pop ? json_encode($pop) : false;

        return $this->set(self::DEFAULT_POP, $pop);
    }

    public function getDefaultDispatchPointId()
    {
        return (int) $this->get(self::DEFAULT_DISPATCH_POINT_ID);
    }

    public function setDefaultDispatchPointId($dispatchPointId)
    {
        return $this->set(self::DEFAULT_DISPATCH_POINT_ID, (int) $dispatchPointId);
    }

    public function getDefaultShipmentReferenceField()
    {
        return (string) $this->get(self::DEFAULT_SHIPMENT_REFERENCE_FIELD);
    }

    public function setDefaultShipmentReferenceField($field)
    {
        return $this->set(self::DEFAULT_SHIPMENT_REFERENCE_FIELD, $field);
    }

    public function getDefaultInsuranceAmount()
    {
        $amount = $this->get(self::DEFAULT_INSURANCE_AMOUNT);

        return $amount > 0. ? (float) $amount : null;
    }

    public function setDefaultInsuranceAmount($amount)
    {
        return $this->set(self::DEFAULT_INSURANCE_AMOUNT, (float) $amount);
    }

    public function setDefaults()
    {
        return $this->setDefaultShipmentReferenceField(ShipmentReferenceFieldChoiceProvider::ORDER_REFERENCE);
    }
}
