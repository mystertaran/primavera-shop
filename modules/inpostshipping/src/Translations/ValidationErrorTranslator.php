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

use InPostShipping;

class ValidationErrorTranslator
{
    const TRANSLATION_SOURCE = 'ValidationErrorTranslator';

    protected $module;

    /**
     * @param InPostShipping $module
     */
    public function __construct(InPostShipping $module)
    {
        $this->module = $module;
    }

    public function translate($error, $fieldName = null)
    {
        $fieldName = empty($fieldName) ? '' : '"' . $fieldName . '"';

        switch ($error) {
            case 'required':
                return sprintf(
                    $this->module->l('Field %s is required', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'invalid':
                return sprintf(
                    $this->module->l('Field %s value is invalid', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'too_short':
                return sprintf(
                    $this->module->l('Field %s value is too short', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'too_long':
                return sprintf(
                    $this->module->l('Field %s value is too long', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'too_small':
                return sprintf(
                    $this->module->l('Field %s value is too small', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'too_big':
                return sprintf(
                    $this->module->l('Field %s value is too big', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            case 'invalid_format':
                return sprintf(
                    $this->module->l('Field %s value is in invalid format', self::TRANSLATION_SOURCE),
                    $fieldName
                );
            default:
                return sprintf(
                    $this->module->l('Error validating field %s: %s', self::TRANSLATION_SOURCE),
                    $fieldName,
                    $error
                );
        }
    }
}
