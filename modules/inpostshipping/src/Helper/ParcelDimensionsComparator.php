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

namespace InPost\Shipping\Helper;

use InPost\Shipping\DataProvider\TemplateDimensionsDataProvider;
use InPost\Shipping\ShipX\Resource\Organization\Shipment;

class ParcelDimensionsComparator
{
    protected $dataProvider;

    protected $templateIndex;

    public function __construct(TemplateDimensionsDataProvider $dataProvider)
    {
        $this->dataProvider = $dataProvider;

        $this->templateIndex = array_flip(Shipment::DIMENSION_TEMPLATES);
    }

    public function compareTemplates($templateA, $templateB)
    {
        if ($templateA === $templateB) {
            return 0;
        }

        return $this->templateIndex[$templateA] > $this->templateIndex[$templateB] ? 1 : -1;
    }

    public function compareDimensions(array $dimensionsA, array $dimensionsB)
    {
        $sumA = array_sum($dimensionsA['dimensions']);
        $sumB = array_sum($dimensionsB['dimensions']);

        if ($sumA > $sumB) {
            return 1;
        }

        if ($sumB > $sumA) {
            return -1;
        }

        if ($dimensionsA['weight']['amount'] === $dimensionsB['weight']['amount']) {
            return 0;
        }

        return $dimensionsA['weight']['amount'] > $dimensionsB['weight']['amount'] ? 1 : -1;
    }

    public function compareTemplateWithDimensions($template, array $dimensions)
    {
        return $this->compareDimensions(
            $this->dataProvider->getDimensions($template),
            $dimensions
        );
    }

    public function getLargestTemplate(array $templates)
    {
        if (empty($templates)) {
            return null;
        }

        $max = array_pop($templates);

        foreach ($templates as $template) {
            if ($this->compareTemplates($template, $max) > 0) {
                $max = $template;
            }
        }

        return $max;
    }
}
