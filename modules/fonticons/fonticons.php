<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-2023 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class fonticons extends Module
{
    public function __construct()
    {
        $this->name = 'fonticons';
        $this->tab = 'other';
        $this->author = 'MyPresta.eu';
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/currency-and-language-by-country.html';
        $this->version = '1.2.3';
        parent::__construct();
        $this->secure_key = Tools::encrypt($this->name);
        $this->bootstrap = true;
        $this->displayName = $this->l('Font awesome + Material icons');
        $this->description = $this->l('Module allows to add support of font awesome and/or material icons to templates, where these iconsets are not available');
        $this->checkforupdates();
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = fonticonsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (fonticonsUpdate::version($this->version) < fonticonsUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = fonticonsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (fonticonsUpdate::version($this->version) < fonticonsUpdate::version(fonticonsUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function inconsistency($ret)
    {
        return true;
    }

    public function install()
    {
        if (parent::install() == false
            || $this->registerHook('displayHeader') == false
            || $this->registerHook('actionAdminControllerSetMedia') == false) {
            return false;
        }
        return true;
    }

    public function hookdisplayHeader($params)
    {
        if (Configuration::get('fonticons_FA') == true) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/font-awesome.css');
        }
        if (Configuration::get('fonticons_MI') == true) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/material-icons.css');
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit')) {
            $this->context->controller->confirmations[] = $this->l('settings saved');
            Configuration::updateValue('fonticons_FA', Tools::getValue('fonticons_FA'));
            Configuration::updateValue('fonticons_MI', Tools::getValue('fonticons_MI'));
            Configuration::updateValue('BO_fonticons_FA', Tools::getValue('BO_fonticons_FA'));
            Configuration::updateValue('BO_fonticons_MI', Tools::getValue('BO_fonticons_MI'));
        }
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        if (Configuration::get('BO_fonticons_FA') == true) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/font-awesome.css');
        }
        if (Configuration::get('BO_fonticons_MI') == true) {
            $this->context->controller->addCSS(($this->_path) . 'views/css/material-icons.css');
        }
    }

    public function displayForm()
    {
        $this->_postProcess();
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-wrench'
                ),
                'input' => array(
                    array(
                        'type' => (version_compare(_PS_VERSION_, '1.6') < 0) ? 'radio' : 'switch',
                        'class' => 't',
                        'label' => $this->l('Add Font Awesome'),
                        'name' => 'fonticons_FA',
                        'values' => array(
                            array(
                                'id' => 'fonticons_FA',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'fonticons_FA',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => (version_compare(_PS_VERSION_, '1.6') < 0) ? 'radio' : 'switch',
                        'class' => 't',
                        'label' => $this->l('Add Material Icons'),
                        'name' => 'fonticons_MI',
                        'values' => array(
                            array(
                                'id' => 'fonticons_MI',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'fonticons_MI',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => (version_compare(_PS_VERSION_, '1.6') < 0) ? 'radio' : 'switch',
                        'class' => 't',
                        'label' => $this->l('Add Font Awesome').' '.$this->l('to BACK OFFICE'),
                        'name' => 'BO_fonticons_FA',
                        'values' => array(
                            array(
                                'id' => 'BO_fonticons_FA',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'BO_fonticons_FA',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => (version_compare(_PS_VERSION_, '1.6') < 0) ? 'radio' : 'switch',
                        'class' => 't',
                        'label' => $this->l('Add Material Icons').' '.$this->l('to BACK OFFICE'),
                        'name' => 'BO_fonticons_MI',
                        'values' => array(
                            array(
                                'id' => 'BO_fonticons_MI',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'BO_fonticons_MI',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->id = 'fonticonsID';
        $helper->identifier = 'fonticons';
        $helper->submit_action = 'btnSubmit';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));

    }

    public function getConfigFieldsValues()
    {
        return array(
            'fonticons_FA' => Tools::getValue('fonticons_FA', Configuration::get('fonticons_FA')),
            'fonticons_MI' => Tools::getValue('fonticons_MI', Configuration::get('fonticons_MI')),
            'BO_fonticons_FA' => Tools::getValue('BO_fonticons_FA', Configuration::get('BO_fonticons_FA')),
            'BO_fonticons_MI' => Tools::getValue('BO_fonticons_MI', Configuration::get('BO_fonticons_MI')),
        );
    }

    public function advert()
    {
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/advert.tpl');
    }

    public function getContent()
    {
        return $this->advert() . $this->displayForm() . $this->checkforupdates(0, true);
    }

    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }
}

class fonticonsUpdate extends fonticons
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}

?>