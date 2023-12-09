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

namespace InPost\Shipping\Handler\ShippingService;

use InPost\Shipping\CarrierConfigurationUpdater;
use InPost\Shipping\CarrierUpdater;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Traits\CommercialProductIdentifierValidatorTrait;
use InPostCarrierModel;
use InPostShipping;
use Validate;

class UpdateServiceHandler
{
    use CommercialProductIdentifierValidatorTrait;

    const TRANSLATION_SOURCE = 'UpdateServiceHandler';

    protected $carrierUpdater;
    protected $configurationUpdater;

    public function __construct(
        InPostShipping $module,
        CarrierUpdater $carrierUpdater,
        CarrierConfigurationUpdater $configurationUpdater
    ) {
        $this->module = $module;
        $this->carrierUpdater = $carrierUpdater;
        $this->configurationUpdater = $configurationUpdater;
    }

    public function handle(array $request)
    {
        $this->resetErrors();

        if (!Validate::isLoadedObject($inPostCarrier = new InPostCarrierModel($request['carrierReference']))) {
            $this->addError(sprintf(
                $this->module->l('No service associated with carrier with reference %s', self::TRANSLATION_SOURCE),
                $request['carrierReference']
            ));

            return null;
        }

        $weekendDelivery = Service::INPOST_LOCKER_STANDARD === $request['service'] && $request['weekendDelivery'];
        $commercialProductIdentifier = Service::INPOST_LOCKER_ECONOMY === $request['service']
            ? (string) $request['commercialProductIdentifier']
            : null;

        if (!$this->validateCommercialProductIdentifier($commercialProductIdentifier)) {
            return null;
        }

        if (!$this->configurationUpdater->update($request)) {
            $this->errors = array_merge(
                $this->errors,
                $this->configurationUpdater->getErrors()
            );
        }

        if ($this->hasErrors()) {
            return null;
        }

        if ($request['updateSettings']) {
            $carrier = $this->carrierUpdater->update(
                $inPostCarrier->getCarrier(),
                $request['service'],
                $request['cashOnDelivery'],
                $weekendDelivery
            );

            if (!$carrier) {
                $this->setErrors($this->carrierUpdater->getErrors());

                return null;
            }
        }

        $inPostCarrier->service = $request['service'];
        $inPostCarrier->cod = $request['cashOnDelivery'];
        $inPostCarrier->weekend_delivery = $weekendDelivery;
        $inPostCarrier->commercial_product_identifier = $commercialProductIdentifier;
        $inPostCarrier->use_product_dimensions = $request['useProductDimensions'];

        $inPostCarrier->update();

        return $inPostCarrier;
    }
}
