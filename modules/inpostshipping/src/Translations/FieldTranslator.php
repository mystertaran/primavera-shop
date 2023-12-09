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

use InPost\Shipping\ChoiceProvider\ShipmentReferenceFieldChoiceProvider;
use InPostShipping;

class FieldTranslator
{
    const TRANSLATION_SOURCE = 'FieldTranslator';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function translate($field)
    {
        static $translations;

        if (!isset($translations)) {
            $translations = [
                ShipmentReferenceFieldChoiceProvider::ORDER_REFERENCE => $this->module->l('Order reference', self::TRANSLATION_SOURCE),
                ShipmentReferenceFieldChoiceProvider::ORDER_ID => $this->module->l('Order ID', self::TRANSLATION_SOURCE),
            ];
        }

        return $translations[$field];
    }
}
