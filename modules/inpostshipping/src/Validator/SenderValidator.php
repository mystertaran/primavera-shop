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

use InPost\Shipping\Traits\PhoneValidatorTrait;
use InPost\Shipping\Translations\ValidationErrorTranslator;
use InPostShipping;
use Tools;
use Validate;

class SenderValidator extends AbstractValidator
{
    use PhoneValidatorTrait;

    const TRANSLATION_SOURCE = 'SenderValidator';

    protected $addressValidator;
    protected $errorTranslator;

    public function __construct(
        InPostShipping $module,
        ValidationErrorTranslator $errorTranslator,
        AddressValidator $addressValidator
    ) {
        parent::__construct($module);

        $this->errorTranslator = $errorTranslator;
        $this->addressValidator = $addressValidator;
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        if (!empty($data)) {
            if (empty($data['email'])) {
                $this->errors['email'] = $this->errorTranslator->translate('required');
            } elseif (Tools::strlen($data['email']) > 255) {
                $this->errors['email'] = $this->errorTranslator->translate('too_long');
            } elseif (!Validate::isEmail($data['email'])) {
                $this->errors['email'] = $this->errorTranslator->translate('invalid_format');
            }

            if (empty($data['phone'])) {
                $this->errors['phone'] = $this->errorTranslator->translate('required');
            } elseif (!$this->validatePhone($data['phone'])) {
                $this->errors['phone'] = $this->errorTranslator->translate('invalid_format');
            }

            foreach (['company_name', 'first_name', 'last_name'] as $field) {
                if (Tools::strlen($data[$field]) > 255) {
                    $this->errors[$field] = $this->errorTranslator->translate('too_long');
                }
            }

            if (empty($data['company_name']) && (empty($data['first_name']) || empty($data['last_name']))) {
                $this->errors[] = $this->module->l('You must provide either a company name or first and last name', self::TRANSLATION_SOURCE);
            }

            if (!$this->addressValidator->validate($data['address'])) {
                $this->setErrors(array_merge($this->errors, $this->addressValidator->getErrors()));
            }
        }

        return !$this->hasErrors();
    }
}
