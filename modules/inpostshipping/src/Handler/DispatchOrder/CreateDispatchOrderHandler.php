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

namespace InPost\Shipping\Handler\DispatchOrder;

use Exception;
use InPost\Shipping\Builder\DispatchOrder\CreateDispatchOrderPayloadBuilder;
use InPost\Shipping\ShipX\Exception\ValidationFailedException;
use InPost\Shipping\ShipX\Resource\Organization\DispatchOrder;
use InPost\Shipping\Traits\ErrorsTrait;
use InPost\Shipping\Translations\ValidationErrorTranslator;
use InPostDispatchOrderModel;
use InPostDispatchPointModel;
use InPostShipping;
use Validate;

class CreateDispatchOrderHandler
{
    use ErrorsTrait;
    const TRANSLATION_SOURCE = 'CreateDispatchOrderHandler';

    const RETRY_NUMBER = 2;

    protected $module;
    protected $errorTranslator;
    protected $payloadBuilder;

    public function __construct(
        InPostShipping $module,
        ValidationErrorTranslator $errorTranslator,
        CreateDispatchOrderPayloadBuilder $dispatchOrderPayloadBuilder
    ) {
        $this->module = $module;
        $this->errorTranslator = $errorTranslator;
        $this->payloadBuilder = $dispatchOrderPayloadBuilder;
    }

    public function handle(array $shipmentIds, $id_dispatch_point)
    {
        $this->resetErrors();

        try {
            $dispatchPoint = new InPostDispatchPointModel($id_dispatch_point);

            if (Validate::isLoadedObject($dispatchPoint)) {
                $payload = $this->payloadBuilder->buildPayload($dispatchPoint, $shipmentIds);

                $this->waitForDispatchOrderNumber(
                    $dispatchOrder = DispatchOrder::create($payload)
                );

                return $this->saveDispatchOrder($dispatchOrder, $dispatchPoint);
            } else {
                $this->addError(
                    $this->module->l('Invalid dispatch point ID', self::TRANSLATION_SOURCE)
                );
            }
        } catch (ValidationFailedException $exception) {
            if ($errors = $exception->getValidationErrors()) {
                $this->translateErrors($errors);
            } else {
                $this->addError($exception->getDetails());
            }
        } catch (Exception $exception) {
            $this->addError($exception->getMessage());
        }

        return false;
    }

    protected function saveDispatchOrder(
        DispatchOrder $dispatchOrder,
        InPostDispatchPointModel $dispatchPoint
    ) {
        $dispatchOrderModel = new InPostDispatchOrderModel();

        $dispatchOrderModel->shipx_dispatch_order_id = $dispatchOrder->getId();
        $dispatchOrderModel->id_dispatch_point = $dispatchPoint->id;
        $dispatchOrderModel->status = $dispatchOrder->status;

        if ($number = $dispatchOrder->external_id) {
            $dispatchOrderModel->number = $number;
        }

        if ($price = $dispatchOrder->price) {
            $dispatchOrderModel->price = $price;
        }

        if ($dispatchOrderModel->add()) {
            return $dispatchOrderModel;
        }

        return false;
    }

    protected function waitForDispatchOrderNumber(DispatchOrder $dispatchOrder)
    {
        $i = 0;

        $number = $dispatchOrder->external_id;
        while (empty($number) && $i++ < self::RETRY_NUMBER) {
            sleep(1);
            $dispatchOrder->refresh();
            $number = $dispatchOrder->external_id;
        }
    }

    protected function translateErrors($errors)
    {
        foreach ($errors as $fieldName => $values) {
            foreach ($values as $error) {
                $this->addError($this->errorTranslator->translate($error, $fieldName));
            }
        }
    }
}
