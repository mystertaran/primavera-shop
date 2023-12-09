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

use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPost\Shipping\ShipX\Resource\SendingMethod;
use InPost\Shipping\Translations\SendingMethodTranslator;

class SendingMethodChoiceProvider implements ChoiceProviderInterface
{
    protected $translator;

    public function __construct(SendingMethodTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function getChoices()
    {
        $choices = [];

        foreach (SendingMethod::SENDING_METHODS as $method) {
            $choices[] = [
                'value' => $method,
                'text' => $this->translator->translate($method),
                'unavailableTemplates' => $this->getUnavailableTemplates($method),
            ];
        }

        return $choices;
    }

    protected function getUnavailableTemplates($method)
    {
        if ($method === SendingMethod::PARCEL_LOCKER) {
            return [Shipment::TEMPLATE_EXTRA_LARGE];
        }

        return [];
    }
}
