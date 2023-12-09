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

use Carrier;
use Context;

class CarrierChoiceProvider implements ChoiceProviderInterface
{
    protected $context;

    public function __construct()
    {
        $this->context = Context::getContext();
    }

    public function getChoices()
    {
        $choices = [];

        foreach (Carrier::getCarriers($this->context->language->id) as $carrier) {
            $choices[$carrier['id_reference']] = [
                'value' => $carrier['id_reference'],
                'text' => $carrier['name'],
            ];
        }

        return $choices;
    }
}
