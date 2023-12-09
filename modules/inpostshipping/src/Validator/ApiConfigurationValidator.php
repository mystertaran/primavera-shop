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
use InPost\Shipping\ShipX\Exception\ResourceNotFoundException;
use InPost\Shipping\ShipX\Exception\TokenInvalidException;
use InPost\Shipping\ShipX\Resource\Organization;
use InPostShipping;

class ApiConfigurationValidator extends AbstractValidator
{
    const TRANSLATION_SOURCE = 'ApiConfigurationValidator';

    protected $shipXConfiguration;

    public function __construct(InPostShipping $module, ShipXConfiguration $shipXConfiguration)
    {
        parent::__construct($module);

        $this->shipXConfiguration = $shipXConfiguration;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        $token = $data['token'];
        $organizationId = (int) $data['organizationId'];
        $sandboxMode = (bool) $data['sandbox'];

        $tokenKey = $sandboxMode ? 'sandboxApiToken' : 'apiToken';
        $organizationIdKey = $sandboxMode ? 'sandboxOrganizationId' : 'organizationId';

        if (empty($token)) {
            $this->errors[$tokenKey] = $this->module->l('Token cannot be empty', self::TRANSLATION_SOURCE);
        }

        if (empty($organizationId)) {
            $this->errors[$organizationIdKey] = $this->module->l('Provided organization ID is invalid', self::TRANSLATION_SOURCE);
        }

        if (empty($this->errors)) {
            $this->shipXConfiguration
                ->setSandboxMode($sandboxMode)
                ->setApiToken($token);

            try {
                $organization = Organization::get($organizationId);

                if ($organization->id !== $organizationId) {
                    $this->errors[$organizationIdKey] = $this->module->l('Access to this organization is not allowed with the provided token', self::TRANSLATION_SOURCE);
                }
            } catch (TokenInvalidException $exception) {
                $this->errors[$tokenKey] = $this->module->l('Provided token is invalid', self::TRANSLATION_SOURCE);
            } catch (AccessForbiddenException $exception) {
                $this->errors[$organizationIdKey] = $this->module->l('Access to this organization is not allowed with the provided token', self::TRANSLATION_SOURCE);
            } catch (ResourceNotFoundException $exception) {
                $this->errors[$organizationIdKey] = $this->module->l('Organization with this ID does not exist', self::TRANSLATION_SOURCE);
            }

            $this->shipXConfiguration
                ->setSandboxMode(null)
                ->setApiToken(null);
        }

        return !$this->hasErrors();
    }
}
