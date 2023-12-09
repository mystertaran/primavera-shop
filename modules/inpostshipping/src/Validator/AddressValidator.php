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

use InPost\Shipping\Translations\ValidationErrorTranslator;
use InPostShipping;
use Tools;

class AddressValidator extends AbstractValidator
{
    protected $errorTranslator;

    public function __construct(
        InPostShipping $module,
        ValidationErrorTranslator $errorTranslator
    ) {
        parent::__construct($module);

        $this->errorTranslator = $errorTranslator;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        foreach (['street', 'building_number', 'city'] as $field) {
            if (empty($data[$field])) {
                $this->errors[$field] = $this->errorTranslator->translate('required');
            } elseif (Tools::strlen($data[$field]) > 255) {
                $this->errors[$field] = $this->errorTranslator->translate('too_long');
            }
        }

        if (empty($data['post_code'])) {
            $this->errors['post_code'] = $this->errorTranslator->translate('required');
        } elseif (!preg_match('/^\d{2}-\d{3}$/', $data['post_code'])) {
            $this->errors['post_code'] = $this->errorTranslator->translate('invalid_format');
        }

        return !$this->hasErrors();
    }
}
