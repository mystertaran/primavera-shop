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

use InPost\Shipping\Views\Modal\CreateDispatchOrderModal;
use InPost\Shipping\Views\Modal\PrintShipmentLabelModal;

abstract class AbstractAdminOrdersHook extends AbstractHook
{
    const HOOK_LIST_177 = [];

    /**
     * @return array
     */
    public function getAvailableHooks()
    {
        return $this->shopContext->is177()
            ? static::HOOK_LIST_177
            : static::HOOK_LIST;
    }

    public function getOutdatedHooks()
    {
        return $this->shopContext->is177()
            ? static::HOOK_LIST
            : [];
    }

    protected function renderPrintShipmentLabelModal()
    {
        /** @var PrintShipmentLabelModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.print_label');

        return $modal
            ->setTemplate($this->getTemplatePath('modal/print-shipment-label.tpl'))
            ->render();
    }

    protected function renderDispatchOrderModal()
    {
        /** @var CreateDispatchOrderModal $modal */
        $modal = $this->module->getService('inpost.shipping.views.modal.dispatch_order');

        return $modal
            ->setTemplate($this->getTemplatePath('modal/create-dispatch-order.tpl'))
            ->render();
    }

    protected function getTemplatePath($template)
    {
        return $this->shopContext->is177()
            ? 'views/templates/hook/177/' . $template
            : 'views/templates/hook/' . $template;
    }
}
