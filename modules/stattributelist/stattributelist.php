<?php
/*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class StAttributeList extends Module
{
    private $_html = '';
    public $fields_form;
    public $fields_value;
    private $_prefix_st = 'ST_ATT_LIST_';
    public $validation_errors = array();
    protected $templatePath;
	function __construct()
	{
		$this->name           = 'stattributelist';
		$this->tab            = 'front_office_features';
		$this->version        = '1.0.0';
		$this->author         = 'SUNNYTOO.COM';
		$this->need_instance  = 0;
		$this->bootstrap 	  = true;
		parent::__construct();

		$this->displayName = $this->l('Prestashop attributes on product listing pages and homepage');
		$this->description = $this->l('Show product attributes out on product listing pages, homepage and some other places.');

        $this->templatePath = 'module:'.$this->name.'/views/templates/hook/';
	}

	function install()
	{
		if (!parent::install() 
            || !Configuration::updateValue($this->_prefix_st.'SHOW', 2)
            || !Configuration::updateValue($this->_prefix_st.'COLOR', 0)
            || !Configuration::updateValue($this->_prefix_st.'CENTER', 1)
            || !Configuration::updateValue($this->_prefix_st.'CUSTOM_CSS', '')
            || !Configuration::updateValue($this->_prefix_st.'HEIGHT', 50)
            || !Configuration::updateValue($this->_prefix_st.'BOTTOM', 0)
            || !Configuration::updateValue($this->_prefix_st.'LISTING', 0)
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayProductListReviews')
        )
			return false;

        $all_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
        foreach ($all_groups as $group){
            Configuration::updateValue($this->_prefix_st.$group['id_attribute_group'], true);                
        }       
		return true;
	}
    public function getContent()
    {
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        $this->initFieldsForm();
        if (isset($_POST['savestattributelist']))
        {
            if (isset($_POST['custom_css']) && $_POST['custom_css'])
                $_POST['custom_css'] = str_replace('\\', '¤', $_POST['custom_css']);

            foreach($this->fields_form as $form)
                foreach($form['form']['input'] as $field)
                    if(isset($field['validation']))
                    {
                        $ishtml = ($field['validation']=='isAnything') ? true : false;
                        $errors = array();       
                        $value = Tools::getValue($field['name']);
                        if (isset($field['required']) && $field['required'] && $value==false && (string)$value != '0')
                                $errors[] = sprintf(Tools::displayError('Field "%s" is required.'), $field['label']);
                        elseif($value)
                        {
                            $field_validation = $field['validation'];
                            if (!Validate::$field_validation($value))
                                $errors[] = sprintf(Tools::displayError('Field "%s" is invalid.'), $field['label']);
                        }
                        // Set default value
                        if ($value === false && isset($field['default_value']))
                            $value = $field['default_value'];
                        
                        if(count($errors))
                        {
                            $this->validation_errors = array_merge($this->validation_errors, $errors);
                        }
                        elseif($value==false)
                        {
                            switch($field['validation'])
                            {
                                case 'isUnsignedId':
                                case 'isUnsignedInt':
                                case 'isInt':
                                case 'isBool':
                                    $value = 0;
                                break;
                                default:
                                    $value = '';
                                break;
                            }
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value);
                        }
                        else
                            Configuration::updateValue($this->_prefix_st.strtoupper($field['name']), $value, $ishtml);
                    }

            $all_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
            foreach ($all_groups as $group){
                Configuration::updateValue($this->_prefix_st.$group['id_attribute_group'], (bool)Tools::getValue('id_attribute_group_'.$group['id_attribute_group']));                
            }        
            $lang_arr = array();
            $all_languages = Language::getLanguages(false);
            foreach ($all_languages as $lang){
                if(Tools::getValue('id_lang_'.$lang['id_lang']))
                    $lang_arr[] = $lang['id_lang'];
            }

            if(count($this->validation_errors))
                $this->_html .= $this->displayError(implode('<br/>',$this->validation_errors));
            else 
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));

            $this->_clearCache('*');
        }

        $helper = $this->initForm();
        
        return $this->_html.$helper->generateForm($this->fields_form).'<div class="alert alert-info">This free module was created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>, it\'s not allow to sell it, it\'s also not allow to create new modules based on this one. Check more <a href="https://www.sunnytoo.com/blogs?term=743&orderby=date&order=desc" target="_blank">free modules</a>, <a href="https://www.sunnytoo.com/product-category/prestashop-modules" target="_blank">advanced paid modules</a> and <a href="https://www.sunnytoo.com/product-category/prestashop-themes" target="_blank">themes(transformer theme and panda  theme)</a> created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>.</div>';
    }

    protected function initFieldsForm()
    {
        $groups_arr = array();
        $all_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
        foreach ($all_groups as $group){
            $groups_arr[] = array(
                'id' => $group['id_attribute_group'],
                'name' => $group['name'],
                'val' => $group['id_attribute_group'],
            );
        }
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->displayName,
                'icon' => 'icon-cogs'
            ),
            'input' => array( 
                array(
                    'type' => 'checkbox',
                    'label' => $this->l('Attribute groups'),
                    'name' => 'id_attribute_group',
                    'lang' => true,
                    'values' => array(
                        'query' => $groups_arr,
                        'id' => 'id',
                        'name' => 'name',
                    ),
                    'desc' => $this->l('You may need to clear the Smarty cache to make changes to this option take effect.'),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Show product attributes:'),
                    'name' => 'show',
                    'values' => array(
                        array(
                            'id' => 'show_off',
                            'value' => 0,
                            'label' => $this->l('NO')),
                        array(
                            'id' => 'show_all',
                            'value' => 1,
                            'label' => $this->l('All')),
                        array(
                            'id' => 'show_in_stock',
                            'value' => 2,
                            'label' => $this->l('In stock only')),
                    ),
                    'validation' => 'isUnsignedInt',
                ), 
                array(
                    'type' => 'radio',
                    'label' => $this->l('How to show color attribute:'),
                    'name' => 'color',
                    'values' => array(
                        array(
                            'id' => 'color_0',
                            'value' => 0,
                            'label' => $this->l('Text')),
                        array(
                            'id' => 'color_1',
                            'value' => 1,
                            'label' => $this->l('Color swatch')),
                    ),
                    'validation' => 'isUnsignedInt',
                ), 
                array(
                    'type' => 'switch',
                    'label' => $this->l('Center align:'),
                    'name' => 'center',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'center_on',
                            'value' => 1,
                            'label' => $this->l('Yes')),
                        array(
                            'id' => 'center_off',
                            'value' => 0,
                            'label' => $this->l('No')),
                    ),
                    'validation' => 'isBool',
                ), 
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show on product listing pages only:'),
                    'name' => 'listing',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'listing_on',
                            'value' => 1,
                            'label' => $this->l('Yes')),
                        array(
                            'id' => 'listing_off',
                            'value' => 0,
                            'label' => $this->l('No')),
                    ),
                    'validation' => 'isBool',
                    'desc' => $this->l('Don not show on home page and product pages.'),
                ), 
                array(
                    'type' => 'text',
                    'label' => $this->l('Increase the height of product information container:'),
                    'name' => 'height',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                    'desc' => $this->l('This option is for the Classic theme. It is used to increase the height of product inforamtion container which contains product name, price and arributes.'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Adjust the position of slide-up block (Option):'),
                    'name' => 'bottom',
                    'prefix' => 'px',
                    'class' => 'fixed-width-lg',
                    'validation' => 'isNullOrUnsignedId',
                    'desc' => $this->l('This option is for the Classic theme. It is used to adjust the position of slide-up block which contains Quick view button. If you are going to set a value, the value should be larger than 70 plus the height you set above.'),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom CSS Code:'),
                    'name' => 'custom_css',
                    'cols' => 60,
                    'rows' => 10,
                    'desc' => $this->l('Put CSS code here without wrapping them in STYLE tag'),
                    'validation' => 'isAnything',
                ),
            ),
            'submit' => array(
                'title' => $this->l('   Save   ')
            )
        );
    }
    protected function initForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->module = $this;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savestattributelist';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        $all_groups = AttributeGroup::getAttributesGroups($this->context->language->id);
        foreach ($all_groups as $group){
             $helper->tpl_vars['fields_value']['id_attribute_group_'.$group['id_attribute_group']] = Configuration::get($this->_prefix_st.$group['id_attribute_group']) ? 1 : 0;             
        }

        return $helper;
    }
    
    private function getConfigFieldsValues()
    {
        $fields_values = array(
            'show' => Configuration::get($this->_prefix_st.'SHOW'),
            'color' => Configuration::get($this->_prefix_st.'COLOR'),
            'center' => Configuration::get($this->_prefix_st.'CENTER'),
            'listing' => Configuration::get($this->_prefix_st.'LISTING'),
            'height' => Configuration::get($this->_prefix_st.'HEIGHT'),
            'bottom' => Configuration::get($this->_prefix_st.'BOTTOM'),
            'custom_css' => str_replace('¤', '\\', Configuration::get($this->_prefix_st.'CUSTOM_CSS')),
        );
        
        return $fields_values;
    }
    public function hookDisplayHeader($params)
    {
        $this->context->controller->addCSS($this->_path.'views/css/front.css');
        if (!$this->isCached($this->templatePath.'header.tpl', $this->getCacheId())) {
            $custom_css = '';
            if($height = (int)Configuration::get($this->_prefix_st.'HEIGHT')){
                $custom_css .= '#products .thumbnail-container, .featured-products .thumbnail-container, .product-accessories .thumbnail-container, .product-miniature .thumbnail-container{height: '.(318+$height).'px;}';
                $custom_css .= '#products .product-description, .featured-products .product-description, .product-accessories .product-description, .product-miniature .product-description{height: '.(70+$height).'px;}';
                $custom_css .= '#products .thumbnail-container:focus .highlighted-informations, #products .thumbnail-container:hover .highlighted-informations, .featured-products .thumbnail-container:focus .highlighted-informations, .featured-products .thumbnail-container:hover .highlighted-informations, .product-accessories .thumbnail-container:focus .highlighted-informations, .product-accessories .thumbnail-container:hover .highlighted-informations, .product-miniature .thumbnail-container:focus .highlighted-informations, .product-miniature .thumbnail-container:hover .highlighted-informations{bottom: '.(70+20+$height).'px;}';
            }
            if($bottom = Configuration::get($this->_prefix_st.'BOTTOM')){
                $custom_css .= '#products .thumbnail-container:focus .highlighted-informations, #products .thumbnail-container:hover .highlighted-informations, .featured-products .thumbnail-container:focus .highlighted-informations, .featured-products .thumbnail-container:hover .highlighted-informations, .product-accessories .thumbnail-container:focus .highlighted-informations, .product-accessories .thumbnail-container:hover .highlighted-informations, .product-miniature .thumbnail-container:focus .highlighted-informations, .product-miniature .thumbnail-container:hover .highlighted-informations{bottom: '.($bottom).'px;}';
                $custom_css .= '#products .thumbnail-container .product-thumbnail, .featured-products .thumbnail-container .product-thumbnail, .product-accessories .thumbnail-container .product-thumbnail, .product-miniature .thumbnail-container .product-thumbnail{height: calc(100% - '.$bottom.'px);}';
            }

            $custom_css .= str_replace('¤', '\\', Configuration::get($this->_prefix_st.'CUSTOM_CSS'));
            $this->context->smarty->assign('st_attr_list_custom_css', html_entity_decode($custom_css));
        }

        return $this->fetch($this->templatePath.'header.tpl', $this->getCacheId());
    }
    public function hookDisplayProductListReviews($params)
    {
        if(!$show = Configuration::get($this->_prefix_st.'SHOW'))
            return false;

        if(Configuration::get($this->_prefix_st.'LISTING') && (!isset($this->context->controller->php_self) || !in_array(Tools::strtolower($this->context->controller->php_self), array('category','search','manufacturer','supplier','new-products','prices-drop','best-sales'))))
            return false;

        if(!$params || !isset($params['product']))
            return false;

        $product = new Product($params['product']['id_product'], false);
        if (!Validate::isLoadedObject($product)) {
            return false;
        }

        $groups = array();
        $attributes_groups = $product->getAttributesGroups($this->context->language->id);
        if (is_array($attributes_groups) && $attributes_groups)
        {
            foreach ($attributes_groups as $k => $row)
            {
                if(!Configuration::get($this->_prefix_st.$row['id_attribute_group']))
                    continue;
                if (!isset($groups[$row['id_attribute_group']]))
                    $groups[$row['id_attribute_group']] = array(
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']]))
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                if($row['group_type']=='color'){
                    $texture = '';
                    if (Tools::isEmpty($row['attribute_color']) && @filemtime(_PS_COL_IMG_DIR_.$row['id_attribute'].'.jpg')) {
                        $texture = $this->context->link->getMediaLink(_THEME_COL_DIR_.$row['id_attribute'].'.jpg');
                    }
                    $groups[$row['id_attribute_group']]['colors'][$row['id_attribute']] = array(
                        'type' => $texture ? 1 : 0,
                        'value' => $texture?:$row['attribute_color'],
                    );
                }
            }
            $this->context->smarty->assign(array(
                'st_att_list_groups' => $groups,
                'st_att_list_show' => $show,
                'st_att_list_color' => Configuration::get($this->_prefix_st.'COLOR'),
                'st_att_list_center' => Configuration::get($this->_prefix_st.'CENTER'),
            ));
            return $this->fetch($this->templatePath.'front.tpl');
        }
        return false;
    }
}