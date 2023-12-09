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

namespace InPost\Shipping\Configuration;

class ShipXConfiguration extends ResettableConfiguration
{
    const API_TOKEN = 'INPOST_SHIPPING_API_TOKEN';
    const ORGANIZATION_ID = 'INPOST_SHIPPING_ORGANIZATION_ID';
    const SANDBOX_MODE_ENABLED = 'INPOST_SHIPPING_SANDBOX_MODE_ENABLED';
    const SANDBOX_API_TOKEN = 'INPOST_SHIPPING_SANDBOX_API_TOKEN';
    const SANDBOX_ORGANIZATION_ID = 'INPOST_SHIPPING_SANDBOX_ORGANIZATION_ID';

    protected $useSandbox;
    protected $apiToken;
    protected $organizationId;

    public function getProductionApiToken()
    {
        return (string) $this->get(self::API_TOKEN);
    }

    public function setProductionApiToken($token)
    {
        return $this->set(self::API_TOKEN, $token);
    }

    public function getProductionOrganizationId()
    {
        return (int) $this->get(self::ORGANIZATION_ID);
    }

    public function setProductionOrganizationId($organizationId)
    {
        return $this->set(self::ORGANIZATION_ID, (int) $organizationId);
    }

    public function isSandboxModeEnabled()
    {
        return (bool) $this->get(self::SANDBOX_MODE_ENABLED);
    }

    public function setSandboxModeEnabled($enabled)
    {
        return $this->set(self::SANDBOX_MODE_ENABLED, (bool) $enabled);
    }

    public function getSandboxApiToken()
    {
        return (string) $this->get(self::SANDBOX_API_TOKEN);
    }

    public function setSandboxApiToken($token)
    {
        return $this->set(self::SANDBOX_API_TOKEN, $token);
    }

    public function getSandboxOrganizationId()
    {
        return (int) $this->get(self::SANDBOX_ORGANIZATION_ID);
    }

    public function setSandboxOrganizationId($organizationId)
    {
        return $this->set(self::SANDBOX_ORGANIZATION_ID, (int) $organizationId);
    }

    public function useSandboxMode()
    {
        return isset($this->useSandbox)
            ? $this->useSandbox
            : $this->isSandboxModeEnabled();
    }

    public function setSandboxMode($enabled)
    {
        $this->useSandbox = null !== $enabled ? (bool) $enabled : null;

        return $this;
    }

    public function getApiToken()
    {
        if (isset($this->apiToken)) {
            return $this->apiToken;
        }

        return $this->useSandboxMode()
            ? $this->getSandboxApiToken()
            : $this->getProductionApiToken();
    }

    public function setApiToken($token)
    {
        $this->apiToken = null !== $token ? (string) $token : null;

        return $this;
    }

    public function getOrganizationId()
    {
        if (isset($this->organizationId)) {
            return $this->organizationId;
        }

        return $this->useSandboxMode()
            ? $this->getSandboxOrganizationId()
            : $this->getProductionOrganizationId();
    }

    public function setOrganizationId($organizationId)
    {
        $this->organizationId = null !== $organizationId ? (int) $organizationId : null;

        return $this;
    }

    public function hasConfiguration()
    {
        return $this->getOrganizationId() && $this->getApiToken();
    }
}
