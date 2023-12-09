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

namespace InPost\Shipping\Hook;

use InPost\Shipping\ChoiceProvider\ProductTemplateChoiceProvider;
use InPost\Shipping\Handler\ProductUpdateHandler;
use InPostProductTemplateModel;
use PrestaShopException;
use Product;
use Tools;

class AdminProduct extends AbstractHook
{
    const HOOK_LIST = [
        'actionAdminProductsControllerSaveAfter',
    ];

    const HOOK_LIST_16 = [
        'displayAdminProductsExtra',
    ];

    const HOOK_LIST_17 = [
        'displayAdminProductsShippingStepBottom',
    ];

    public function hookActionAdminProductsControllerSaveAfter($params)
    {
        /** @var Product $product */
        if ($product = $params['return']) {
            /** @var ProductUpdateHandler $updater */
            $updater = $this->module->getService('inpost.shipping.handler.product_update');

            $template = Tools::getValue('inpost_dimension_template') ?: null;

            if (!$updater->update($product, $template)) {
                throw new PrestaShopException(current($updater->getErrors()));
            }
        }
    }

    public function hookDisplayAdminProductsExtra()
    {
        $this->assignTemplateVariables(Tools::getValue('id_product'));

        return $this->module->display($this->module->name, 'views/templates/hook/16/admin-products-form.tpl');
    }

    public function hookDisplayAdminProductsShippingStepBottom($params)
    {
        $this->assignTemplateVariables($params['id_product']);

        return $this->module->display($this->module->name, 'views/templates/hook/admin-products-form.tpl');
    }

    protected function assignTemplateVariables($id_product)
    {
        /** @var ProductTemplateChoiceProvider $templateChoiceProvider */
        $templateChoiceProvider = $this->module->getService('inpost.shipping.choice_provider.product_template');
        $productTemplate = new InPostProductTemplateModel($id_product);

        $this->context->smarty->assign([
            'templateChoices' => $templateChoiceProvider->getChoices(),
            'selectedTemplate' => $productTemplate->template,
        ]);
    }
}
