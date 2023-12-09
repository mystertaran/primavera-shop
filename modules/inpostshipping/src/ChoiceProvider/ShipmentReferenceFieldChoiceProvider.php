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

use InPost\Shipping\Translations\FieldTranslator;

class ShipmentReferenceFieldChoiceProvider implements ChoiceProviderInterface
{
    const TRANSLATION_SOURCE = 'ShipmentReferenceFieldChoiceProvider';

    const ORDER_REFERENCE = 'reference';
    const ORDER_ID = 'id_order';

    const FIELDS = [
        self::ORDER_REFERENCE,
        self::ORDER_ID,
    ];

    protected $translator;

    public function __construct(FieldTranslator $translator)
    {
        $this->translator = $translator;
    }

    public function getChoices()
    {
        $choices = [];

        foreach (self::FIELDS as $field) {
            $choices[] = [
                'value' => $field,
                'text' => $this->translator->translate($field),
            ];
        }

        return $choices;
    }
}
