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

namespace InPost\Shipping\DataProvider;

use Exception;
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\Organization;
use InPost\Shipping\Traits\ErrorsTrait;

class OrganizationDataProvider
{
    use ErrorsTrait;

    protected $shipXConfiguration;

    protected $organization;

    public function __construct(ShipXConfiguration $shipXConfiguration)
    {
        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function getOrganizationData()
    {
        if (!isset($this->organization)) {
            $this->organization = false;

            if ($organizationId = $this->shipXConfiguration->getOrganizationId()) {
                try {
                    $this->organization = Organization::get($organizationId)->toArray();
                } catch (Exception $exception) {
                    if ($exception instanceof TokenInvalidException ||
                        $exception instanceof ResourceNotFoundException
                    ) {
                        $this->errors['authorization'] = $exception->getMessage();
                    } else {
                        $this->errors['exception'] = $exception->getMessage();
                    }
                }
            }
        }

        return $this->organization;
    }
}
