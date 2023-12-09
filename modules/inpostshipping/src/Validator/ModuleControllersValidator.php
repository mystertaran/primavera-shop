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

use InPost\Shipping\ChoiceProvider\ModulePageChoiceProvider;
use InPostShipping;

class ModuleControllersValidator extends AbstractValidator
{
    const TRANSLATION_SOURCE = 'ModuleControllersValidator';

    protected $pageChoices;

    public function __construct(
        InPostShipping $module,
        ModulePageChoiceProvider $pageChoiceProvider
    ) {
        parent::__construct($module);

        $this->pageChoices = $pageChoiceProvider->getChoices();
    }

    public function validate(array $data)
    {
        $this->resetErrors();

        foreach ($data as $moduleName => $controllers) {
            if (!isset($this->pageChoices[$moduleName])) {
                $this->addError(sprintf(
                    $this->module->l('Module "%s" does not exist', self::TRANSLATION_SOURCE),
                    $moduleName
                ));
            } elseif (empty($controllers)) {
                $this->addError(sprintf(
                    $this->module->l('No controller selected for module "%s"', self::TRANSLATION_SOURCE),
                    $moduleName
                ));
            } else {
                foreach ($controllers as $controller) {
                    $validControllers = array_column($this->pageChoices[$moduleName], 'value');

                    if (!in_array($controller, $validControllers)) {
                        $this->addError(sprintf(
                            $this->module->l('Controller "%s" is not valid for module "%s"', self::TRANSLATION_SOURCE),
                            $controller,
                            $moduleName
                        ));
                    }
                }
            }
        }

        return !$this->hasErrors();
    }
}
