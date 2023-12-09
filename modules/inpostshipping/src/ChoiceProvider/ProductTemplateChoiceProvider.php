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

use InPost\Shipping\ShipX\Resource\Service;
use InPost\Shipping\Translations\DimensionTemplateTranslator;

class ProductTemplateChoiceProvider extends DimensionTemplateChoiceProvider
{
    protected $serviceChoiceProvider;

    public function __construct(
        DimensionTemplateTranslator $translator,
        ShippingServiceChoiceProvider $serviceChoiceProvider
    ) {
        parent::__construct($translator);

        $this->serviceChoiceProvider = $serviceChoiceProvider;
    }

    protected function getAvailableTemplates()
    {
        return $this->serviceChoiceProvider->getAvailableTemplates(Service::INPOST_LOCKER_STANDARD);
    }
}
