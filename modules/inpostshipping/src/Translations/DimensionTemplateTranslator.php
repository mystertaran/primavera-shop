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

namespace InPost\Shipping\Translations;

use InPost\Shipping\ShipX\Resource\Organization\Shipment;
use InPostShipping;

class DimensionTemplateTranslator
{
    const TRANSLATION_SOURCE = 'DimensionTemplateTranslator';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function translate($template)
    {
        static $translations;

        if (!isset($translations)) {
            $translations = [
                Shipment::TEMPLATE_SMALL => $this->module->l('Size A', self::TRANSLATION_SOURCE),
                Shipment::TEMPLATE_MEDIUM => $this->module->l('Size B', self::TRANSLATION_SOURCE),
                Shipment::TEMPLATE_LARGE => $this->module->l('Size C', self::TRANSLATION_SOURCE),
                Shipment::TEMPLATE_EXTRA_LARGE => $this->module->l('Size D', self::TRANSLATION_SOURCE),
                Shipment::TEMPLATE_PARCEL => $this->module->l('Parcel', self::TRANSLATION_SOURCE),
                Shipment::TEMPLATE_PALETTE => $this->module->l('Pallet', self::TRANSLATION_SOURCE),
            ];
        }

        return isset($translations[$template]) ? $translations[$template] : $template;
    }
}
