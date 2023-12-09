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

namespace InPost\Shipping\Validator;

use InPost\Shipping\ChoiceProvider\WeekdayChoiceProvider;

class WeekendDeliveryConfigurationValidator extends AbstractValidator
{
    const TRANSLATION_SOURCE = 'WeekendDeliveryConfigurationValidator';

    public function validate(array $data)
    {
        $this->resetErrors();

        foreach (['startDay', 'endDay'] as $field) {
            if (!in_array($data[$field], WeekdayChoiceProvider::WEEKDAYS)) {
                $this->errors[$field] = $this->module->l('Selected day value is invalid', self::TRANSLATION_SOURCE);
            }
        }

        foreach (['startHour', 'endHour'] as $field) {
            if (!preg_match('/^([0-1]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $data[$field])) {
                $this->errors[$field] = $this->module->l('Invalid hour format', self::TRANSLATION_SOURCE);
            }
        }

        return !$this->hasErrors();
    }
}
