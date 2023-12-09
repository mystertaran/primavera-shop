<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Columnsconfigurator extends Module implements WidgetInterface
{
    private $templateFile;

    public function __construct()
    {
        $this->name = 'columnsconfigurator';
        $this->author = 'BelVG';
        $this->version = '1.0.0';

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('BelVG Product Columns Configurator', array(), 'Modules.Columnsconfigurator.Admin');
        $this->description = $this->trans('Allows you to change number of products per row.', array(), 'Modules.Columnsconfigurator.Admin');

        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);

        $this->templateFile = 'module:columnsconfigurator/columnsconfigurator.tpl'; 
    }

    public function install()
    {
	    $this->_clearCache('*');
	    
        return (parent::install() &&
            Configuration::updateValue('COLUMN_VAL_HP', 3) &&
            Configuration::updateValue('COLUMN_VAL_CP', 2) &&
            $this->registerHook('displayTop') &&
            $this->registerHook('displayHeader'));
    }

    public function uninstall()
    {
        return (
            Configuration::deleteByName('COLUMN_VAL_HP') &&
            Configuration::deleteByName('COLUMN_VAL_CP') &&
            parent::uninstall());
    }
    
    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }  

    public function getContent()
    {
        if (Tools::isSubmit('submitModule')) {
        	        
            Configuration::updateValue('COLUMN_VAL_HP', Tools::getValue('column_value_hp', ''));
            Configuration::updateValue('COLUMN_VAL_CP', Tools::getValue('column_value_cp', ''));

            $this->_clearCache('*');

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules').'&configure='.$this->name.'&tab_module='.$this->tab.'&conf=4&module_name='.$this->name);

        }
		return $this->renderForm();
    }
    
    public function hookdisplayHeader($params)
    {
        $this->context->controller->registerStylesheet('modules-columnsconfigurator', 'modules/'.$this->name.'/css/columnsconfigurator.css', ['media' => 'all', 'priority' => 150]);	    
        $this->context->controller->registerJavascript('modules-columnsconfigurator', 'modules/'.$this->name.'/js/columnsconfigurator.js', ['position' => 'bottom', 'priority' => 150]);  
    }       

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->trans('Settings', array(), 'Admin.Global'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Number of products per row on Homepage (Minimum 2, Maximum 12):', array(), 'Modules.Columnsconfigurator.Admin'),
                        'name' => 'column_value_hp',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Define number of products per row on Homepage.', array(), 'Modules.Columnsconfigurator.Admin'),
                    ), 
                    array(
                        'type' => 'text',
                        'label' => $this->trans('Number of products per row on Category Page (Minimum 2, Maximum 12):', array(), 'Modules.Columnsconfigurator.Admin'),
                        'name' => 'column_value_cp',
                        'class' => 'fixed-width-xs',
                        'desc' => $this->trans('Define number of products per row on Category Page.', array(), 'Modules.Columnsconfigurator.Admin'),
                    ),                                                        
                 ),
                'submit' => array(
                    'title' => $this->trans('Save', array(), 'Admin.Global'),
                )
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->submit_action = 'submitModule';
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'column_value_hp' => Tools::getValue('column_value_hp', Configuration::get('COLUMN_VAL_HP')),
            'column_value_cp' => Tools::getValue('column_value_cp', Configuration::get('COLUMN_VAL_CP')),
        );
    } 

    public function renderWidget($hookName = null, array $configuration = [])
    {
            $this->smarty->assign('belvg_column_value_hp', Configuration::get('COLUMN_VAL_HP'));
            $this->smarty->assign('belvg_column_value_cp', Configuration::get('COLUMN_VAL_CP'));
            
        if (!$this->isCached($this->templateFile, $this->getCacheId('columnsconfigurator'))) {
            $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        }

        return $this->fetch($this->templateFile, $this->getCacheId('columnsconfigurator'));
    }
 
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $column = array();

			$column_value_hp = Configuration::get('COLUMN_VAL_HP');
            $column['column_value_hp'] = array(
                'label' => $this->trans('Number of products on homepage', array(), 'Modules.Columnsconfigurator.Shop'),
                'class' => 'column',
                'value' => $column_value_hp,
            ); 
			$column_value_cp = Configuration::get('COLUMN_VAL_CP');
            $column['column_value_cp'] = array(
                'label' => $this->trans('Number of products on category page', array(), 'Modules.Columnsconfigurator.Shop'),
                'class' => 'column',
                'value' => $column_value_cp,
            );                              
         
        return array( 
            'column' => $column,
        );
    }
 
}
