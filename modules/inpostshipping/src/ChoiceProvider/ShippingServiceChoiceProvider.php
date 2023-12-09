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

namespace InPost\Shipping\ChoiceProvider;

use InPost\Shipping\DataProvider\OrganizationDataProvider;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Translations\ShippingServiceTranslator;

class ShippingServiceChoiceProvider implements ChoiceProviderInterface
{
    protected $organizationDataProvider;
    protected $translator;

    protected $availableServices;

    public function __construct(
        OrganizationDataProvider $organizationDataProvider,
        ShippingServiceTranslator $translator
    ) {
        $this->organizationDataProvider = $organizationDataProvider;
        $this->translator = $translator;
    }

    public function getChoices()
    {
        $choices = [];

        foreach (Service::SERVICES as $service) {
            $choices[$service] = [
                'value' => $service,
                'text' => $this->translator->translate($service),
                'disabled' => !$this->isAvailable($service),
                'availableTemplates' => $this->getAvailableTemplates($service),
                'availableSendingMethods' => $this->getAvailableSendingMethods($service),
            ];
        }

        return $choices;
    }

    protected function isAvailable($service)
    {
        if (!isset($this->availableServices)) {
            if ($organization = $this->organizationDataProvider->getOrganizationData()) {
                $this->availableServices = array_flip($organization['services']);
            } else {
                $this->availableServices = [];
            }
        }

        return isset($this->availableServices[$service]);
    }

    public function getAvailableTemplates($service)
    {
        switch ($service) {
            case Service::INPOST_LOCKER_STANDARD:
            case Service::INPOST_LOCKER_ECONOMY:
                return [
                    Shipment::TEMPLATE_SMALL,
                    Shipment::TEMPLATE_MEDIUM,
                    Shipment::TEMPLATE_LARGE,
                ];
            case Service::INPOST_COURIER_C2C:
                return [
                    Shipment::TEMPLATE_SMALL,
                    Shipment::TEMPLATE_MEDIUM,
                    Shipment::TEMPLATE_LARGE,
                    Shipment::TEMPLATE_EXTRA_LARGE,
                ];
            default:
                return [];
        }
    }

    public function getAvailableSendingMethods($service)
    {
        switch ($service) {
            case Service::INPOST_LOCKER_STANDARD:
            case Service::INPOST_LOCKER_ECONOMY:
            case Service::INPOST_COURIER_C2C:
                return SendingMethod::SENDING_METHODS;
            case Service::INPOST_COURIER_LOCAL_STANDARD:
            case Service::INPOST_COURIER_LOCAL_EXPRESS:
            case Service::INPOST_COURIER_LOCAL_SUPER_EXPRESS:
                return [SendingMethod::DISPATCH_ORDER];
            default:
                return [
                    SendingMethod::DISPATCH_ORDER,
                    SendingMethod::POP,
                ];
        }
    }
}
