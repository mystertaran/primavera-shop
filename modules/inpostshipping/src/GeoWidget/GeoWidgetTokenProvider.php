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

namespace InPost\Shipping\GeoWidget;

use Context;
use FrontController;
use InPost\Shipping\Configuration\GeoWidgetConfiguration;
use InPost\Shipping\Configuration\ShipXConfiguration;

class GeoWidgetTokenProvider
{
    protected $geoWidgetConfiguration;
    protected $shipXConfiguration;
    protected $context;

    private $token;

    public function __construct(
        GeoWidgetConfiguration $geoWidgetConfiguration,
        ShipXConfiguration $shipXConfiguration
    ) {
        $this->geoWidgetConfiguration = $geoWidgetConfiguration;
        $this->shipXConfiguration = $shipXConfiguration;
        $this->context = Context::getContext();
    }

    public function getToken()
    {
        if (!isset($this->token)) {
            if ($sandbox = $this->shouldUseSandbox()) {
                $token = $this->geoWidgetConfiguration->getSandboxToken();
            } else {
                $token = $this->geoWidgetConfiguration->getProductionToken();
            }

            $this->token = $token
                ? new GeoWidgetToken($token, $sandbox)
                : false;
        }

        return $this->token ?: null;
    }

    protected function shouldUseSandbox()
    {
        return $this->shipXConfiguration->isSandboxModeEnabled()
            && (
                !$this->context->controller instanceof FrontController
                || $this->geoWidgetConfiguration->shouldUseSandboxOnTheFrontOffice()
            );
    }
}
