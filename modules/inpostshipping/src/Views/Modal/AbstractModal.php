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

namespace InPost\Shipping\Views\Modal;

use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\PrestaShopContext;
use InPost\Shipping\Views\AbstractRenderable;
use InPostShipping;

abstract class AbstractModal extends AbstractRenderable
{
    const MODAL_ID = '';

    protected $shopContext;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        PrestaShopContext $shopContext
    ) {
        parent::__construct($module, $link);

        $this->shopContext = $shopContext;
    }

    /** @return array */
    public function getModalData()
    {
        return [
            'modal_id' => static::MODAL_ID,
            'modal_class' => $this->getClasses(),
            'modal_title' => $this->getTitle(),
            'modal_content' => $this->renderContent(),
            'modal_actions' => $this->getActions(),
        ];
    }

    /** @return string */
    protected function getClasses()
    {
        return 'modal-md';
    }

    /** @return string */
    abstract protected function getTitle();

    /** @return string */
    public function renderContent()
    {
        $this->assignContentTemplateVariables();

        return $this->module->display($this->module->name, $this->template);
    }

    abstract protected function assignContentTemplateVariables();

    /** @return array */
    abstract protected function getActions();

    public function assignTemplateVariables()
    {
        $this->context->smarty->assign($this->getModalData());
    }

    public function render()
    {
        $this->assignTemplateVariables();

        return $this->shopContext->is177()
            ? $this->module->display($this->module->name, 'views/templates/hook/177/modal/modal.tpl')
            : $this->context->smarty->fetch('modal.tpl');
    }
}
