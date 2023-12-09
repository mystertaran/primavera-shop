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

use InPost\Shipping\Views\DispatchPointNavTabs;

require_once dirname(__FILE__) . '/InPostShippingAdminController.php';

class AdminInPostDispatchPointsController extends InPostShippingAdminController
{
    const TRANSLATION_SOURCE = 'AdminInPostDispatchPointsController';

    /** @var InPostDispatchPointModel */
    protected $object;

    public function __construct()
    {
        $this->table = 'inpost_dispatch_point';
        $this->identifier = 'id_dispatch_point';
        $this->bootstrap = true;

        parent::__construct();

        $this->className = InPostDispatchPointModel::class;

        $this->_where .= ' AND a.deleted = 0';

        $this->fields_list = [
            'name' => [
                'title' => $this->module->l('Name', self::TRANSLATION_SOURCE),
            ],
            'street' => [
                'title' => $this->module->l('Street', self::TRANSLATION_SOURCE),
            ],
            'building_number' => [
                'title' => $this->module->l('Building number', self::TRANSLATION_SOURCE),
            ],
            'post_code' => [
                'title' => $this->module->l('Postal code', self::TRANSLATION_SOURCE),
            ],
            'city' => [
                'title' => $this->module->l('City', self::TRANSLATION_SOURCE),
            ],
        ];

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->module->l('Delete selected', self::TRANSLATION_SOURCE),
                'icon' => 'icon-trash',
                'confirm' => $this->module->l('Delete selected items?', self::TRANSLATION_SOURCE),
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        if ($this->shopContext->is17()) {
            $this->module->getAssetsManager()
                ->registerJavaScripts([
                    'admin/dispatch-points.js',
                ])
                ->registerStyleSheets([
                    'admin/table-fix.css',
                ]);
        } else {
            $this->module->getAssetsManager()
                ->registerStyleSheets([
                    'admin/nav-tabs.css',
                    'admin/table-fix.css',
                ]);
        }
    }

    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_dispatch_point'] = [
                'href' => $this->link->getAdminLink($this->controller_name, true, [], [
                    'add' . $this->table => true,
                ]),
                'desc' => $this->module->l('Add a new dispatch point', self::TRANSLATION_SOURCE),
                'icon' => 'process-icon-new',
            ];
        }

        parent::initPageHeaderToolbar();
    }

    public function renderForm()
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Dispatch point'),
                'icon' => 'icon-truck',
            ],
            'input' => [
                'name' => [
                    'type' => 'text',
                    'label' => $this->module->l('Name', self::TRANSLATION_SOURCE),
                    'name' => 'name',
                    'required' => true,
                    'maxlength' => 255,
                    'col' => 6,
                ],
                'office_hours' => [
                    'type' => 'text',
                    'label' => $this->module->l('Office hours', self::TRANSLATION_SOURCE),
                    'name' => 'office_hours',
                    'maxlength' => 255,
                    'col' => 6,
                ],
                'email' => [
                    'type' => 'text',
                    'label' => $this->module->l('Email', self::TRANSLATION_SOURCE),
                    'name' => 'email',
                    'maxlength' => 255,
                    'col' => 6,
                ],
                'phone' => [
                    'type' => 'text',
                    'label' => $this->module->l('Phone', self::TRANSLATION_SOURCE),
                    'name' => 'phone',
                    'maxlength' => 255,
                    'col' => 6,
                ],
                'street' => [
                    'type' => 'text',
                    'label' => $this->module->l('Street', self::TRANSLATION_SOURCE),
                    'required' => true,
                    'name' => 'street',
                    'maxlength' => 255,
                    'col' => 6,
                ],
                'building_number' => [
                    'type' => 'text',
                    'label' => $this->module->l('Building number', self::TRANSLATION_SOURCE),
                    'required' => true,
                    'name' => 'building_number',
                    'maxlength' => 255,
                    'col' => 2,
                ],
                'post_code' => [
                    'type' => 'text',
                    'label' => $this->module->l('Postal code', self::TRANSLATION_SOURCE),
                    'hint' => $this->module->l('Polish format (xx-xxx)', self::TRANSLATION_SOURCE),
                    'required' => true,
                    'name' => 'post_code',
                    'maxlength' => 6,
                    'col' => 2,
                ],
                'city' => [
                    'type' => 'text',
                    'label' => $this->module->l('City', self::TRANSLATION_SOURCE),
                    'required' => true,
                    'name' => 'city',
                    'maxlength' => 255,
                    'col' => 6,
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save', self::TRANSLATION_SOURCE),
            ],
        ];

        return parent::renderForm();
    }

    public function processAdd()
    {
        if (($result = parent::processAdd()) && $back = Tools::getValue('back')) {
            $this->redirect_after = urldecode($back);
        }

        return $result;
    }

    protected function _childValidation()
    {
        if (!preg_match('/^\d{2}-\d{3}$/', Tools::getValue('post_code'))) {
            $this->errors['post_code'] = $this->module->l('Invalid postal code format', self::TRANSLATION_SOURCE);
        }
    }

    protected function renderNavTabs()
    {
        /** @var DispatchPointNavTabs $view */
        $view = $this->module->getService('inpost.shipping.views.dispatch_point_nav_tabs');

        return $view->render();
    }

    protected function shouldRenderNavTabs($content)
    {
        return $content === $this->layout;
    }

    public function initHeader()
    {
        parent::initHeader();

        if ($this->shopContext->is17()) {
            $this->context->smarty->assign([
                'current_tab_level' => 3,
            ]);
        }
    }
}
