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

namespace InPost\Shipping\Validator;

use InPost\Shipping\Translations\ValidationErrorTranslator;
use InPostShipping;
use OrderState;
use Validate;

class OrdersConfigurationValidator extends AbstractValidator
{
    protected $errorTranslator;

    public function __construct(
        InPostShipping $module,
        ValidationErrorTranslator $errorTranslator
    ) {
        parent::__construct($module);

        $this->errorTranslator = $errorTranslator;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        if ($data['changeOrderStateOnShipmentLabelPrinted']) {
            $this->validateOrderStateId($data, 'shipmentLabelPrintedOrderStateId');
        }

        if ($data['changeOrderStateOnShipmentDelivered']) {
            $this->validateOrderStateId($data, 'shipmentDeliveredOrderStateId');
        }

        return !$this->hasErrors();
    }

    protected function validateOrderStateId(array $data, $key)
    {
        if (empty($data[$key])) {
            $this->errors[$key] = $this->errorTranslator->translate('required');
        } else {
            $orderState = new OrderState($data[$key]);

            if (!Validate::isLoadedObject($orderState)) {
                $this->errors[$key] = $this->errorTranslator->translate('invalid');
            }
        }
    }
}
