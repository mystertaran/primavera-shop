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

use Exception;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPostShipmentModel;
use Order;
use PrestaShopCollection;

class BulkCreateShipmentHandler extends CreateShipmentHandler
{
    const TRANSLATION_SOURCE = 'BulkCreateShipmentHandler';

    public function handle(array $request)
    {
        $this->resetErrors();

        if (empty($request['orderIds'])) {
            $this->addError($this->module->l('No orders selected', self::TRANSLATION_SOURCE));

            return [];
        }

        $orders = (new PrestaShopCollection(Order::class))
            ->where('id_order', 'IN', $request['orderIds']);

        if (0 === count($orders)) {
            $this->addError($this->module->l('Invalid order IDs', self::TRANSLATION_SOURCE));

            return [];
        }

        $shipments = [];

        /** @var Order $order */
        foreach ($orders as $order) {
            try {
                if ($payload = $this->payloadBuilder->buildPayload($order)) {
                    $shipments[] = $this->saveShipment($order, Shipment::create($payload));
                }
            } catch (ValidationFailedException $exception) {
                if ($errors = $exception->getValidationErrors()) {
                    foreach ($this->translateErrors($errors) as $error) {
                        $this->addOrderError($order->reference, $error);
                    }
                } else {
                    $this->addError($exception->getDetails());
                }
            } catch (Exception $exception) {
                $this->addOrderError($order->reference, $exception->getMessage());
            }
        }

        try {
            $this->waitForTransactionsData($shipments);
        } catch (Exception $exception) {
            $this->addError($exception->getMessage());
        }

        if (empty($shipments) && !$this->hasErrors()) {
            $this->addError($this->module->l('None of the selected orders were placed with an InPost carrier as a delivery option', self::TRANSLATION_SOURCE));
        }

        return $shipments;
    }

    /** @param InPostShipmentModel[] $shipments */
    private function waitForTransactionsData(array $shipments)
    {
        $shipmentIds = [];
        $remaining = [];

        foreach ($shipments as $shipmentModel) {
            $remaining[$shipmentModel->shipx_shipment_id] = $shipmentModel;
            $shipmentIds[$shipmentModel->shipx_shipment_id] = $shipmentModel->shipx_shipment_id;
        }

        $i = 0;
        while (!empty($remaining) && $i++ < self::REFRESH_RETRY_NUMBER) {
            sleep(1);

            foreach (Shipment::getCollection(['id' => $shipmentIds]) as $shipment) {
                $transactions = $shipment->transactions;
                if (!empty($transactions)) {
                    $shipmentModel = $remaining[$shipment->id];

                    $transaction = current($transactions);
                    if ($transaction['status'] !== 'success') {
                        $this->addOrderError(
                            $shipmentModel->getOrder()->reference,
                            $this->getTransactionError($transaction)
                        );
                    } else {
                        $shipmentModel->status = $shipment->status;
                        $shipmentModel->tracking_number = $shipment->tracking_number;
                        $shipmentModel->update();
                        $shipmentModel->updateOrderTrackingNumber();
                    }

                    unset(
                        $remaining[$shipmentModel->shipx_shipment_id],
                        $shipmentIds[$shipmentModel->shipx_shipment_id]
                    );
                }
            }
        }
    }

    protected function addOrderError($reference, $error)
    {
        return $this->addError(sprintf(
            $this->module->l('Order "%s": %s', self::TRANSLATION_SOURCE),
            $reference,
            $error
        ));
    }
}
