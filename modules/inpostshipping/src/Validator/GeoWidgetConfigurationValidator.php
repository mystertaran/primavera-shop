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

use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Exception\AccessForbiddenException;
use InPost\Shipping\ShipX\Exception\ShipXException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\NewApiPoint;
use InPostShipping;

class GeoWidgetConfigurationValidator extends AbstractValidator
{
    const TRANSLATION_SOURCE = 'GeoWidgetConfigurationValidator';

    protected $shipXConfiguration;

    public function __construct(
        InPostShipping $module,
        ShipXConfiguration $shipXConfiguration
    ) {
        parent::__construct($module);

        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        if (!empty($token = $data['token'])) {
            $this->validateToken($token);
        }

        if (!empty($sandboxToken = $data['sandboxToken'])) {
            $this->validateToken($sandboxToken, true);
        }

        return !$this->hasErrors();
    }

    protected function validateToken($token, $sandboxMode = false)
    {
        $this->shipXConfiguration
            ->setSandboxMode($sandboxMode)
            ->setApiToken($token);

        try {
            $collection = NewApiPoint::getCollection([], '', '', 1);

            count($collection);
        } catch (ShipXException $exception) {
            if ($exception instanceof AccessForbiddenException || $exception instanceof TokenInvalidException) {
                $errorKey = $sandboxMode ? 'sandboxToken' : 'token';

                $this->errors[$errorKey] = $this->module->l('Provided token is not valid for your shop domain', self::TRANSLATION_SOURCE);
            } else {
                throw $exception;
            }
        }

        $this->shipXConfiguration
            ->setSandboxMode(null)
            ->setApiToken(null);
    }
}
