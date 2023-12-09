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

use InPost\Shipping\ShipX\Resource\Organization\Shipment;

class TemplateDimensionsDataProvider
{
    public function getDimensions($template)
    {
        switch ($template) {
            case Shipment::TEMPLATE_SMALL:
                return [
                    'dimensions' => [
                        'length' => 80.,
                        'width' => 380.,
                        'height' => 640.,
                    ],
                    'weight' => [
                        'amount' => 25.,
                    ],
                ];
            case Shipment::TEMPLATE_MEDIUM:
                return [
                    'dimensions' => [
                        'length' => 190.,
                        'width' => 380.,
                        'height' => 640.,
                    ],
                    'weight' => [
                        'amount' => 25.,
                    ],
                ];
            case Shipment::TEMPLATE_LARGE:
                return [
                    'dimensions' => [
                        'length' => 410.,
                        'width' => 380.,
                        'height' => 640.,
                    ],
                    'weight' => [
                        'amount' => 25.,
                    ],
                ];
            case Shipment::TEMPLATE_EXTRA_LARGE:
                return [
                    'dimensions' => [
                        'length' => 500.,
                        'width' => 500.,
                        'height' => 800.,
                    ],
                    'weight' => [
                        'amount' => 25.,
                    ],
                ];
            default:
                return null;
        }
    }
}
