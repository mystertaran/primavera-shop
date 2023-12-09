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

namespace InPost\Shipping\Views;

use Context;
use InPost\Shipping\Adapter\LinkAdapter;
use InPostShipping;

abstract class AbstractRenderable implements RenderableInterface
{
    protected $module;

    protected $context;
    protected $link;

    protected $template;

    public function __construct(InPostShipping $module, LinkAdapter $link)
    {
        $this->module = $module;
        $this->context = Context::getContext();
        $this->link = $link;
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    abstract protected function assignTemplateVariables();

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $this->assignTemplateVariables();

        return $this->module->display($this->module->name, $this->template);
    }
}
