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

namespace InPost\Shipping\Handler\Shipment;

use InPost\Shipping\Configuration\OrdersConfiguration;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPostShipmentModel;
use OrderState;
use Validate;

class PrintShipmentLabelHandler
{
    protected $ordersConfiguration;

    protected $labelPrintedOrderState;

    public function __construct(OrdersConfiguration $ordersConfiguration)
    {
        $this->ordersConfiguration = $ordersConfiguration;
    }

    public function handle(InPostShipmentModel $shipmentModel, array $options = [])
    {
        $label = Shipment::getLabel($shipmentModel->shipx_shipment_id, $options);
        $this->updateShipment($shipmentModel);

        return $label;
    }

    /** @param InPostShipmentModel[] $shipmentModels */
    public function handleMultiple(array $shipmentModels, array $options = [])
    {
        $shipmentIds = [];
        foreach ($shipmentModels as $shipmentModel) {
            $shipmentIds[] = $shipmentModel->shipx_shipment_id;
        }

        $labels = Shipment::getMultipleLabels($shipmentIds, $options);

        foreach ($shipmentModels as $shipmentModel) {
            $this->updateShipment($shipmentModel);
        }

        return $labels;
    }

    protected function updateShipment(InPostShipmentModel $shipmentModel)
    {
        if ($shipmentModel->label_printed) {
            return;
        }

        $shipmentModel->label_printed = true;
        $shipmentModel->update();

        $this->updateOrderStatus($shipmentModel);
    }

    protected function updateOrderStatus(InPostShipmentModel $shipmentModel)
    {
        if (!$this->ordersConfiguration->shouldChangeOrderStateOnShipmentLabelPrinted() ||
            !$orderState = $this->getLabelPrintedOrderState()
        ) {
            return;
        }

        $currentState = $shipmentModel->getOrder()->getCurrentOrderState();

        if (null === $currentState || $currentState->id !== $orderState->id) {
            $shipmentModel->getOrder()->setCurrentState($orderState->id);
        }
    }

    protected function getLabelPrintedOrderState()
    {
        if (!isset($this->labelPrintedOrderState)) {
            $orderStateId = $this->ordersConfiguration->getShipmentLabelPrintedOrderStateId();

            if (Validate::isLoadedObject($orderState = new OrderState($orderStateId))) {
                $this->labelPrintedOrderState = $orderState;
            } else {
                $this->labelPrintedOrderState = false;
            }
        }

        return $this->labelPrintedOrderState ?: null;
    }
}
