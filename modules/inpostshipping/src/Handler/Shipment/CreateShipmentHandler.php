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
use InPost\Shipping\Builder\Shipment\CreateShipmentPayloadBuilder;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Traits\ErrorsTrait;
use InPost\Shipping\Translations\ValidationErrorTranslator;
use InPostShipmentModel;
use InPostShipping;
use Order;
use Validate;

class CreateShipmentHandler
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'CreateShipmentHandler';
    const REFRESH_RETRY_NUMBER = 5;

    protected $module;
    protected $errorTranslator;
    protected $payloadBuilder;
    protected $shipXConfiguration;

    public function __construct(
        InPostShipping $module,
        ValidationErrorTranslator $errorTranslator,
        CreateShipmentPayloadBuilder $payloadBuilder,
        ShipXConfiguration $shipXConfiguration
    ) {
        $this->module = $module;
        $this->errorTranslator = $errorTranslator;
        $this->payloadBuilder = $payloadBuilder;
        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function handle(array $request)
    {
        $this->resetErrors();

        if (!isset($request['service'])) {
            $this->addError($this->module->l('Selected shipping service is invalid or unavailable', self::TRANSLATION_SOURCE));
        } elseif (!Validate::isLoadedObject($order = new Order($request['id_order']))) {
            $this->addError($this->module->l('Invalid order ID', self::TRANSLATION_SOURCE));
        } else {
            try {
                $payload = $this->payloadBuilder->buildPayload($order, $request);

                $transaction = $this->waitForTransactionData($shipment = Shipment::create($payload));
                if (!$transaction || $transaction['status'] !== 'success') {
                    $this->addError(
                        $this->getTransactionError($transaction)
                    );
                }

                return $this->saveShipment($order, $shipment);
            } catch (ValidationFailedException $exception) {
                if ($errors = $exception->getValidationErrors()) {
                    foreach ($this->translateErrors($errors) as $error) {
                        $this->addError($error);
                    }
                } else {
                    $this->addError($exception->getDetails());
                }
            } catch (Exception $exception) {
                $this->addError($exception->getMessage());
            }
        }

        return false;
    }

    protected function translateErrors($errors)
    {
        $result = [];

        foreach ($errors as $fieldName => $values) {
            foreach ($values as $error) {
                $result[] = $this->errorTranslator->translate($error, $fieldName);
            }
        }

        return $result;
    }

    protected function saveShipment(Order $order, Shipment $shipment)
    {
        $shipmentModel = new InPostShipmentModel();

        $service = $shipment->service;
        if (Service::INPOST_LOCKER_CUSTOMER_SERVICE_POINT === $service) {
            $service = Service::INPOST_LOCKER_STANDARD;
        }

        $shipmentModel->setOrder($order);
        $shipmentModel->organization_id = $this->shipXConfiguration->getOrganizationId();
        $shipmentModel->sandbox = $this->shipXConfiguration->useSandboxMode();
        $shipmentModel->shipx_shipment_id = $shipment->getId();
        $shipmentModel->reference = in_array($service, Service::LOCKER_CARRIER_SERVICES, true)
            ? $shipment->reference
            : $shipment->comments;
        $shipmentModel->email = $shipment->receiver['email'];
        $shipmentModel->phone = $shipment->receiver['phone'];
        $shipmentModel->service = $service;
        $shipmentModel->tracking_number = $shipment->tracking_number;
        $shipmentModel->status = $shipment->status;

        if (($selected_offer = $shipment->selected_offer) && $selected_offer['rate']) {
            $shipmentModel->price = $selected_offer['rate'];
        }

        $parcels = $shipment->parcels;
        $parcel = current($parcels);
        if (isset($parcel['template']) && $parcel['template']) {
            $shipmentModel->template = $parcel['template'];
        } else {
            $shipmentModel->dimensions = json_encode([
                'length' => $parcel['dimensions']['length'],
                'width' => $parcel['dimensions']['length'],
                'height' => $parcel['dimensions']['height'],
                'weight' => $parcel['weight']['amount'],
            ]);
        }

        $customAttributes = $shipment->custom_attributes;
        if (isset($customAttributes['sending_method'])) {
            $shipmentModel->sending_method = $customAttributes['sending_method'];
        }
        if (isset($customAttributes['dropoff_point'])) {
            $shipmentModel->sending_point = $customAttributes['dropoff_point'];
        }
        if (isset($customAttributes['target_point'])) {
            $shipmentModel->target_point = $customAttributes['target_point'];
        }

        $cod = $shipment->cod;
        if (isset($cod['amount'])) {
            $shipmentModel->cod_amount = $cod['amount'];
        }

        $insurance = $shipment->insurance;
        if (isset($insurance['amount'])) {
            $shipmentModel->insurance_amount = $insurance['amount'];
        }

        if (Service::INPOST_LOCKER_STANDARD === $service) {
            $shipmentModel->weekend_delivery = $shipment->end_of_week_collection;
        } elseif (Service::INPOST_LOCKER_ECONOMY === $service) {
            $shipmentModel->commercial_product_identifier = $shipment->commercial_product_identifier;
        }

        $shipmentModel->add();
        $shipmentModel->updateOrderTrackingNumber();

        return $shipmentModel;
    }

    private function waitForTransactionData(Shipment $shipment)
    {
        $i = 0;

        $transactions = $shipment->transactions;
        while (empty($transactions) && $i++ < self::REFRESH_RETRY_NUMBER) {
            sleep(1);
            $shipment->refresh();
            $transactions = $shipment->transactions;
        }

        return $transactions ? current($transactions) : null;
    }

    protected function getTransactionError($transaction)
    {
        if (!isset($transaction)) {
            return $this->module->l('Shipment has been created, however processing data by the InPost servers takes longer than expected. Please try to refresh the shipment status later.', self::TRANSLATION_SOURCE);
        }

        if ($transaction['details']) {
            if ($transaction['details']['error'] === 'validation_failed') {
                $details = $transaction['details']['details'];
            } else {
                $details = $transaction['details']['message'];
            }

            $message = sprintf(
                $this->module->l('Message: %s', self::TRANSLATION_SOURCE),
                $details
            );
        } else {
            $message = '';
        }

        return sprintf(
            $this->module->l('Transaction failed. %s', self::TRANSLATION_SOURCE),
            $message
        );
    }
}
