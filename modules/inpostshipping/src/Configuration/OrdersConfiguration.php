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

class OrdersConfiguration extends ResettableConfiguration
{
    const ORDER_CONF_DISPLAY_LOCKER = 'INPOST_SHIPPING_ORDER_CONF_DISPLAY_LOCKER';
    const CHANGE_OS_SHIPMENT_LABEL_PRINTED = 'INPOST_SHIPPING_CHANGE_OS_SHIPMENT_LABEL_PRINTED';
    const SHIPMENT_LABEL_PRINTED_OS_ID = 'INPOST_SHIPPING_SHIPMENT_LABEL_PRINTED_OS_ID';
    const CHANGE_OS_SHIPMENT_DELIVERED = 'INPOST_SHIPPING_CHANGE_OS_SHIPMENT_DELIVERED';
    const SHIPMENT_DELIVERED_OS_ID = 'INPOST_SHIPPING_SHIPMENT_DELIVERED_OS_ID';

    public function shouldDisplayOrderConfirmationLocker()
    {
        return (bool) $this->get(self::ORDER_CONF_DISPLAY_LOCKER);
    }

    public function setDisplayOrderConfirmationLocker($display)
    {
        return $this->set(self::ORDER_CONF_DISPLAY_LOCKER, (bool) $display);
    }

    public function shouldChangeOrderStateOnShipmentLabelPrinted()
    {
        return (bool) $this->get(self::CHANGE_OS_SHIPMENT_LABEL_PRINTED);
    }

    public function setChangeOrderStateOnShipmentLabelPrinted($change)
    {
        return $this->set(self::CHANGE_OS_SHIPMENT_LABEL_PRINTED, (bool) $change);
    }

    public function getShipmentLabelPrintedOrderStateId()
    {
        return (int) $this->get(self::SHIPMENT_LABEL_PRINTED_OS_ID);
    }

    public function setShipmentLabelPrintedOrderStateId($orderStateId)
    {
        return $this->set(self::SHIPMENT_LABEL_PRINTED_OS_ID, (int) $orderStateId);
    }

    public function shouldChangeOrderStateOnShipmentDelivered()
    {
        return (bool) $this->get(self::CHANGE_OS_SHIPMENT_DELIVERED);
    }

    public function setChangeOrderStateOnShipmentDelivered($change)
    {
        return $this->set(self::CHANGE_OS_SHIPMENT_DELIVERED, (bool) $change);
    }

    public function getShipmentDeliveredOrderStateId()
    {
        return (int) $this->get(self::SHIPMENT_DELIVERED_OS_ID);
    }

    public function setShipmentDeliveredOrderStateId($orderStateId)
    {
        return $this->set(self::SHIPMENT_DELIVERED_OS_ID, (int) $orderStateId);
    }
}
