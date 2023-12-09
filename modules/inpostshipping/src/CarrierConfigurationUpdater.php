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

namespace InPost\Shipping;

use InPost\Shipping\ChoiceProvider\ShippingServiceChoiceProvider;
use InPost\Shipping\Configuration\CarriersConfiguration;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Traits\ErrorsTrait;
use InPost\Shipping\Validator\ShipmentDimensionsValidator;
use InPostShipping;

class CarrierConfigurationUpdater
{
    use ErrorsTrait;

    const TRANSLATION_SOURCE = 'CarrierConfigurationUpdater';

    protected $module;
    protected $carriersConfiguration;
    protected $serviceChoiceProvider;
    protected $dimensionsValidator;

    public function __construct(
        InPostShipping $module,
        CarriersConfiguration $carriersConfiguration,
        ShippingServiceChoiceProvider $serviceChoiceProvider,
        ShipmentDimensionsValidator $dimensionsValidator
    ) {
        $this->module = $module;
        $this->carriersConfiguration = $carriersConfiguration;
        $this->serviceChoiceProvider = $serviceChoiceProvider;
        $this->dimensionsValidator = $dimensionsValidator;
    }

    public function update(array $request)
    {
        $this->resetErrors();

        if (!in_array($service = $request['service'], Service::SERVICES, true)) {
            $this->errors['service'] = $this->module->l('Invalid shipping service', self::TRANSLATION_SOURCE);

            return false;
        }

        $sendingMethod = $request['defaultSendingMethod'];
        if (!in_array($sendingMethod, $this->serviceChoiceProvider->getAvailableSendingMethods($service), true)) {
            $this->errors['defaultSendingMethod'] = $this->module->l('This sending method is not available for the selected service', self::TRANSLATION_SOURCE);
        }

        if (in_array($service, Service::LOCKER_CARRIER_SERVICES, true) && $template = $request['defaultTemplate']) {
            $dimensions = [];
            if (!in_array($template, $this->serviceChoiceProvider->getAvailableTemplates($service), true)) {
                $this->errors['defaultTemplate'] = $this->module->l('This template is not available for the selected service', self::TRANSLATION_SOURCE);
            }
        } else {
            $template = null;
            $dimensions = array_map(static function ($dimension) {
                return (float) str_replace(',', '.', $dimension);
            }, json_decode($request['defaultDimensions'], true));

            if (!$this->dimensionsValidator->validate($dimensions)) {
                $this->errors = array_merge($this->errors, $this->dimensionsValidator->getErrors());
            }
        }

        if ($this->hasErrors()) {
            return false;
        }

        $this->carriersConfiguration->setDefaultDimensionTemplate($service, $template);
        $this->carriersConfiguration->setDefaultShipmentDimensions($service, $dimensions);
        $this->carriersConfiguration->setDefaultSendingMethod($service, $sendingMethod);

        return true;
    }
}
