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

use InPost\Shipping\Adapter\LinkAdapter;
use InPost\Shipping\Install\Tabs;
use InPostShipping;

/* IGNORE_THIS_FILE_FOR_TRANSLATION */
class ShipmentNavTabs extends AbstractRenderable
{
    public function __construct(InPostShipping $module, LinkAdapter $link)
    {
        parent::__construct($module, $link);

        $this->setTemplate('views/templates/admin/nav-tabs.tpl');
    }

    protected function assignTemplateVariables()
    {
        $this->context->smarty->assign('navTabs', $this->getTabs());
    }

    protected function getTabs()
    {
        $tabs = [];

        foreach ($this->getTabNames() as $controller => $name) {
            $tabs[] = [
                'name' => $name,
                'href' => $this->link->getAdminLink($controller),
                'current' => $this->context->controller->controller_name === $controller,
            ];
        }

        return $tabs;
    }

    protected function getTabNames()
    {
        return [
            Tabs::SHIPMENTS_CONTROLLER_NAME => $this->module->l('Confirmed shipments', Tabs::TRANSLATION_SOURCE),
            Tabs::DISPATCH_ORDERS_CONTROLLER_NAME => $this->module->l('Dispatch orders', Tabs::TRANSLATION_SOURCE),
            Tabs::SENT_SHIPMENTS_CONTROLLER_NAME => $this->module->l('Sent shipments', Tabs::TRANSLATION_SOURCE),
        ];
    }
}
