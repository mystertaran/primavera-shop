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
use InPost\Shipping\Configuration\ShipXConfiguration;
use InPost\Shipping\Install\Tabs;
use InPost\Shipping\Translations\Translations;
use InPostShipping;
use Tools;

/* IGNORE_THIS_FILE_FOR_TRANSLATION */
class DispatchPointNavTabs extends AbstractRenderable
{
    protected $configuration;
    protected $translations;

    public function __construct(
        InPostShipping $module,
        LinkAdapter $link,
        ShipXConfiguration $configuration,
        Translations $translations
    ) {
        parent::__construct($module, $link);

        $this->configuration = $configuration;
        $this->translations = $translations;

        $this->setTemplate('views/templates/admin/nav-tabs.tpl');
    }

    protected function assignTemplateVariables()
    {
        $this->context->smarty->assign('navTabs', $this->getTabs());
    }

    public function getTabs()
    {
        $tabs = [];

        foreach ($this->getAvailableConfigPages() as $page) {
            $tabs[] = $this->getConfigPageTab($page);
        }

        array_splice($tabs, -3, 0, [
            [
                'name' => $this->getConfigPageName('dispatch_points'),
                'href' => $this->link->getAdminLink(Tabs::DISPATCH_POINT_CONTROLLER_NAME),
                'current' => true,
            ],
        ]);

        return $tabs;
    }

    protected function getConfigPageTab($page)
    {
        return [
            'name' => $this->getConfigPageName($page),
            'href' => $this->link->getAdminLink('AdminModules', true, [], [
                'configure' => $this->module->name,
            ]) . '#/' . $page,
            'current' => false,
        ];
    }

    protected function getConfigPageName($page)
    {
        static $pageNames;

        if (!isset($pageNames)) {
            $pageNames = $this->translations->getNavMenuTranslations();
        }

        $key = Tools::toCamelCase($page);

        return isset($pageNames[$key]) ? $pageNames[$key] : $key;
    }

    protected function getAvailableConfigPages()
    {
        return $this->configuration->hasConfiguration()
            ? [
                'authorization',
                'geo_widget',
                'organization',
                'sending',
                'services',
                'weekend_delivery',
                'checkout',
                'orders',
                'szybkie_zwroty',
            ]
            : [
                'authorization',
                'checkout',
                'orders',
                'szybkie_zwroty',
            ];
    }
}
