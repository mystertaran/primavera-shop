<?php
/**
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(dirname(__FILE__).'/classes/CustomHtmlClass.php');
include_once(dirname(__FILE__).'/sql/SampleDataHtml.php');
class belvgCustomHTML extends Module
{
	protected $error = false;
	private $_html;
	private $myHook = array('displayNav', 'displayNav1', 'displayNav2', 'displayLeftLogo', 'displayRightLogo', 'displayTop','displayTopColumn', 'displayRightSlider', 'displayRightHome', 'displayBanner','displayLeftColumn','displayRightColumn','displayFooter','displayHome', 'displayTopHome', 'displayCenterHome', 'displayHomeFooter', 'displayTopHomeRight', 'displayBottomHome', 'displayAfterMenu', 'displayBottomFooter', 'displayBannerCategory');
	public function __construct()
	{
		$this->name = 'belvgcustomhtml';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'belvg';
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('belvg Custom Html');
		$this->description = $this->l('Adds Html blocks to hook');
		$this->confirmUninstall = $this->l('Are you sure that you want to delete your HTML blocks?');
	}
	
	public function install()
	{	
		if (parent::install() == false || !$this->registerHook('header') || !$this->registerHook('actionShopDataDuplication') || !$this->registerHook('actionObjectLanguageAddAfter'))
			return false;
		foreach ($this->myHook as $hook)
			if (!$this->registerHook($hook))
				return false;
		include(dirname(__FILE__).'/sql/install.php');
		$sample_data = new SampleDataHtml();
		$base_url = _PS_BASE_URL_.__PS_BASE_URI__;
		$sample_data->initData($base_url);
		return true;
	}
	public function uninstall()
	{
		include(dirname(__FILE__).'/sql/uninstall.php');
		if (parent::uninstall() == false)
			return false;
		return true;
	}
	public function getContent()
	{
		if (Tools::isSubmit('changeStatus') || Tools::isSubmit('delete_cat_block'))
		{
			$this->_postProcess();
			$this->_html .= $this->renderList();
		}
		elseif (Tools::isSubmit('addblock') || Tools::isSubmit('id_belvgcustomhtml') || Tools::isSubmit('saveBlock'))
		{
			$this->_postProcess();
			$this->renderAddForm();
		}
		else
		{
			$this->_postProcess();
			$this->_html .= $this->renderList();
		}
		$this->_displayAdvertising();
		return $this->_html;
	}
	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('saveBlock'))
		{
			$block = new CustomHtmlClass(Tools::getValue('id_belvgcustomhtml'));
			$block->copyFromPost();
			$errors = $block->validateController();		
			if (count($errors))
				$this->_html .= $this->displayError(implode('<br />', $errors));
			else
			{
				Tools::getValue('id_belvgcustomhtml') ? $block->update() : $block->add();
				$this->clearCacheBlock(Tools::getValue('hook'));
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&saveBlockConfirmation');
			}
		}
		elseif (Tools::isSubmit('changeStatus') && Tools::getValue('id_belvgcustomhtml'))
		{
			$block = new CustomHtmlClass(Tools::getValue('id_belvgcustomhtml'));
			if ($block->active == 0)
				$block->active = 1;
			else
				$block->active = 0;
			$res = $block->update();
			$this->clearCacheBlock(Tools::getValue('hook'));
			$this->html .= ($res ? $this->displayConfirmation($this->l('Configuration updated')) : $this->displayError($this->l('The configuration could not be updated.')));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'));
		}
		elseif (Tools::isSubmit('delete_id_block'))
		{
			$block = new CustomHtmlClass(Tools::getValue('delete_id_block'));
			$block->delete();
			$this->clearCacheBlock(Tools::getValue('hook'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&deleteBlockConfirmation');
		}
		elseif (Tools::isSubmit('saveBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block has been saved successfully'));
		elseif (Tools::isSubmit('deleteBlockConfirmation'))
			$this->_html = $this->displayConfirmation($this->l('Static block deleted successfully'));
	}
	private function clearCacheBlock($hook)
	{
		$this->_clearCache('belvgcustomhtml_'.Tools::strtolower($this->getHookName($hook)).'.tpl');
	}
	private function getHookName($id_hook, $name = false)
	{
		if (!$result = Db::getInstance()->getRow('
		SELECT `name`,`title`
		FROM `'._DB_PREFIX_.'hook` 
		WHERE `id_hook` = '.(int)($id_hook)))
			return false;
		return $result['name'];
	}
	private function getBlocks()
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		if (!$result = Db::getInstance()->ExecuteS(
			'SELECT b.id_belvgcustomhtml, b.hook, bs.active, bl.`title`, bl.`content` 
			FROM `'._DB_PREFIX_.'belvgcustomhtml` b 
			LEFT JOIN `'._DB_PREFIX_.'belvgcustomhtml_shop` bs ON (b.`id_belvgcustomhtml` = bs.`id_belvgcustomhtml` )
			LEFT JOIN `'._DB_PREFIX_.'belvgcustomhtml_lang` bl ON (b.`id_belvgcustomhtml` = bl.`id_belvgcustomhtml`'.( $id_shop ? 'AND bl.`id_shop` = '.$id_shop : ' ' ).') 
			WHERE bl.id_lang = '.(int)$id_lang.
			( $id_shop ? ' AND bs.`id_shop` = '.$id_shop : ' ' )))
			return false;
		return $result;
	}
	
	public function getHookTitle($id_hook, $name = false)
	{
		if (!$result = Db::getInstance()->getRow('
			SELECT `name`,`title`
			FROM `'._DB_PREFIX_.'hook` 
			WHERE `id_hook` = '.(int)($id_hook)))
			return false;
		return (($result['title'] != '' && $name) ? $result['title'] : $result['name']);
	}
	public function renderList()
	{
		$blocks = array();
		if ($this->getBlocks() && count($this->getBlocks()) > 0)
		{
			$blocks = $this->getBlocks();
			foreach ($blocks as $key => $block)
				$blocks[$key]['status'] = $this->displayStatus($block['id_belvgcustomhtml'], $block['active']);
		}
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'blocks' => $blocks
			)
		);
		return $this->display(__FILE__, 'views/templates/admin/list.tpl');
	}
	public function getHookList()
	{
		$hooks = array();
		
		foreach ($this->myHook as $key => $hook)
		{
			$hooks[$key]['key'] = $hook;
			$hooks[$key]['name'] = $hook;
		}
		return $hooks;
	}
	public function renderAddForm()
	{
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$hooks = $this->getHookList();
		$this->fields_form[0]['form'] = array(
					'tinymce' => true,
					'legend' => array(
					'title' => $this->l('Block item'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('Title'),
					'lang' => true,
					'name' => 'title',
					'cols' => 40,
					'rows' => 10
					
				),
				array(
					'type' => 'select',
					'label' => $this->l('Hook'),
					'name' => 'hook',
					'options' => array(
						'query' => $hooks, 
						'id' => 'key',
						'name' => 'name'
					)
					
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('HTML'),
					'name' => 'content',
					'lang' => true,
					'autoload_rte' => true,
					'cols' => 40,
					'rows' => 10
				),
				array(
						'type' => 'switch',
						'label' => $this->l('Displayed'),
						'name' => 'active',
						'values' => array(
									array(
										'id' => 'active_on',
										'value' => 1,
										'label' => $this->l('Enabled')
									),
									array(
										'id' => 'active_off',
										'value' => 0,
										'label' => $this->l('Disabled')
									)
						),
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'belvgcustomhtml';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->show_cancel_button = true;
		$helper->back_url = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules');
		foreach (Language::getLanguages(false) as $lang)
			$helper->languages[] = array(
				'id_lang' => $lang['id_lang'],
				'iso_code' => $lang['iso_code'],
				'name' => $lang['name'],
				'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0)
			);

		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveBlock';
		$helper->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $this->l('Save'),
				'href' => $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' =>
			array(
				'href' => $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $this->l('Back to list')
			)
		);
		
		$id_belvgcustomhtml = Tools::getValue('id_belvgcustomhtml');
		if (Tools::isSubmit('id_belvgcustomhtml') && $id_belvgcustomhtml)
		{
			$block = new CustomHtmlClass((int)$id_belvgcustomhtml);
			$this->fields_form[0]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_belvgcustomhtml');
			$helper->fields_value['id_belvgcustomhtml'] = (int)Tools::getValue('id_belvgcustomhtml', $block->id_belvgcustomhtml);
			$helper->fields_value['active'] = Tools::getValue('active', $block->active);
			$helper->fields_value['hook'] = Tools::getValue('hook', $block->hook);
		}
		else
		{
			$block = new CustomHtmlClass();
			$helper->fields_value['active'] = Tools::getValue('active', 1);
			$helper->fields_value['hook'] = Tools::getValue('hook', 9);
		}
		foreach (Language::getLanguages(false) as $lang)
		{
			$helper->fields_value['title'][(int)$lang['id_lang']] = Tools::getValue('title_'.(int)$lang['id_lang'], $block->title[(int)$lang['id_lang']]);
			$helper->fields_value['content'][(int)$lang['id_lang']] = Tools::getValue('content_'.(int)$lang['id_lang'], $block->content[(int)$lang['id_lang']]);
		}
		
		$this->_html .= $helper->generateForm($this->fields_form);
	}
	
public function _displayAdvertising()
{
		$this->_html .= '
		<br/>
		<fieldset>
			<legend>'.$this->l('More Themes & Modules').'</legend>	
			<iframe src="http://waterthemes.com/advertising/prestashop_advertising.html" width="100%" height="420px;" border="0" style="border:none;"></iframe>
			</fieldset>';
		return $this->_html;
}
	public function displayStatus($id_belvgcustomhtml, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		
		$this->context->smarty->assign(
			array(
				'title' => $title,
				'icon' => $icon,
				'class' => $class,
				'id_belvgcustomhtml' => $id_belvgcustomhtml,
				'link' => AdminController::$currentIndex,
				'name' => $this->name,
				'token' => Tools::getAdminTokenLite('AdminModules'),
			)
		);
		
		return $this->display(__FILE__, 'views/templates/admin/displaystatus.tpl');
	}
	
	private function getBlockFromHook($hook_name)
	{
		$block_list = array();
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		
		$results = Db::getInstance()->ExecuteS('SELECT b.`id_belvgcustomhtml`, bl.`content` FROM `'._DB_PREFIX_.'belvgcustomhtml` b
		LEFT JOIN `'._DB_PREFIX_.'belvgcustomhtml_shop` bs ON (b.id_belvgcustomhtml = bs.id_belvgcustomhtml)
		LEFT JOIN `'._DB_PREFIX_.'belvgcustomhtml_lang` bl ON (bl.id_belvgcustomhtml = bs.id_belvgcustomhtml)
		WHERE bs.active = 1 AND bl.id_lang = '.(int)$id_lang.' AND (bs.id_shop = '.(int)$id_shop.') AND b.`hook` = "'.$hook_name.'"');
	
		 //$block_list = [];
		  foreach ($results as $result) {
			  $temp = str_replace('{static_block_url}', _PS_BASE_URL_.__PS_BASE_URI__, $result['content']);
            $block_list[$result['id_belvgcustomhtml']]['content'] = $temp;
        }
		return $block_list;
	}
	public function hookDisplayNav()
	{
		if (!$this->isCached('belvgcustomhtml_displaynav.tpl', $this->getCacheId('belvgcustomhtml_displaynav')))
		{
			$block_list = $this->getBlockFromHook('displayNav');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaynav.tpl', $this->getCacheId('belvgcustomhtml_displaynav'));
	}
	public function hookDisplayNav1()
	{
		if (!$this->isCached('belvgcustomhtml_displaynav.tpl', $this->getCacheId('belvgcustomhtml_displaynav')))
		{
			$block_list = $this->getBlockFromHook('displayNav1');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaynav1.tpl', $this->getCacheId('belvgcustomhtml_displaynav1'));
	}
	public function hookDisplayNav2()
	{
		if (!$this->isCached('belvgcustomhtml_displaynav2.tpl', $this->getCacheId('belvgcustomhtml_displaynav2')))
		{
			$block_list = $this->getBlockFromHook('displayNav2');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaynav.tpl', $this->getCacheId('belvgcustomhtml_displaynav'));
	}
	public function hookDisplayTop()
	{
		if (!$this->isCached('belvgcustomhtml_top.tpl', $this->getCacheId('belvgcustomhtml_top')))
		{
			$block_list = $this->getBlockFromHook('displayTop');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_top.tpl', $this->getCacheId('belvgcustomhtml_top'));
	}
	public function hookDisplayLeftLogo()
	{
		if (!$this->isCached('belvgcustomhtml_leftlogo.tpl', $this->getCacheId('belvgcustomhtml_leftlogo')))
		{
			$block_list = $this->getBlockFromHook('displayLeftLogo');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_leftlogo.tpl', $this->getCacheId('belvgcustomhtml_leftlogo'));
	}
	public function hookDisplayRightLogo()
	{
		if (!$this->isCached('belvgcustomhtml_rightlogo.tpl', $this->getCacheId('belvgcustomhtml_rightlogo')))
		{
			$block_list = $this->getBlockFromHook('displayRightLogo');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_rightlogo.tpl', $this->getCacheId('belvgcustomhtml_rightlogo'));
	}
	public function hookDisplayRightHome()
	{
		if (!$this->isCached('belvgcustomhtml_righthome.tpl', $this->getCacheId('belvgcustomhtml_righthome')))
		{
			$block_list = $this->getBlockFromHook('displayRightHome');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_righthome.tpl', $this->getCacheId('belvgcustomhtml_righthome'));
	}
	public function hookDisplayRightSlider()
	{
		if (!$this->isCached('belvgcustomhtml_rightslider.tpl', $this->getCacheId('belvgcustomhtml_rightslider')))
		{
			$block_list = $this->getBlockFromHook('displayRightSlider');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_rightslider.tpl', $this->getCacheId('belvgcustomhtml_rightslider'));
	}
	public function hookDisplayTopColumn()
	{
		if ($this->context->smarty->tpl_vars['page_name']->value == 'index')
		{
			if (!$this->isCached('belvgcustomhtml_topcolumn.tpl', $this->getCacheId('belvgcustomhtml_topcolumn')))
			{
				$block_list = $this->getBlockFromHook('displayTopColumn');
				
				$this->context->smarty->assign(array(
					'block_list' => $block_list,
					'page_name' => $this->context->controller->php_self
				));
			}
		return $this->display(__FILE__, 'belvgcustomhtml_topcolumn.tpl', $this->getCacheId('belvgcustomhtml_topcolumn'));
		}
	}
	
	public function hookDisplayLeftColumn()
	{
		if (!$this->isCached('belvgcustomhtml_leftcolumn.tpl', $this->getCacheId('belvgcustomhtml_leftcolumn')))
		{
			$block_list = $this->getBlockFromHook('displayLeftColumn');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_leftcolumn.tpl', $this->getCacheId('belvgcustomhtml_leftcolumn'));
	}
	
	public function hookDisplayRightColumn()
	{
		if (!$this->isCached('belvgcustomhtml_rightcolumn.tpl', $this->getCacheId('belvgcustomhtml_rightcolumn')))
		{
			$block_list = $this->getBlockFromHook('displayRightColumn');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_rightcolumn.tpl', $this->getCacheId('belvgcustomhtml_rightcolumn'));
	}
	
	public function hookDisplayFooter()
	{
		if (!$this->isCached('belvgcustomhtml_footer.tpl', $this->getCacheId('belvgcustomhtml_footer')))
		{
			$block_list = $this->getBlockFromHook('displayFooter');
			
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_footer.tpl', $this->getCacheId('belvgcustomhtml_footer'));
	}
	
	public function hookDisplayHome()
	{
		if (!$this->isCached('belvgcustomhtml_home.tpl', $this->getCacheId('belvgcustomhtml_home')))
		{
			$block_list = $this->getBlockFromHook('displayHome');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_home.tpl', $this->getCacheId('belvgcustomhtml_home'));
	}
	public function hookDisplayTopHome()
	{
		if (!$this->isCached('belvgcustomhtml_displaytophome.tpl', $this->getCacheId('belvgcustomhtml_displaytophome')))
		{
			$block_list = $this->getBlockFromHook('displayTopHome');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaytophome.tpl', $this->getCacheId('belvgcustomhtml_displaytophome'));
	}
	public function hookDisplayCenterHome()
	{
		if (!$this->isCached('belvgcustomhtml_displaycenterhome.tpl', $this->getCacheId('belvgcustomhtml_displaycenterhome')))
		{
			$block_list = $this->getBlockFromHook('displayCenterHome');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaycenterhome.tpl', $this->getCacheId('belvgcustomhtml_displaycenterhome'));
	}
	public function hookDisplayHomeFooter()
	{
		if (!$this->isCached('belvgcustomhtml_displayhomefooter.tpl', $this->getCacheId('belvgcustomhtml_displayhomefooter')))
		{
			$block_list = $this->getBlockFromHook('displayHomeFooter');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaycenterhome.tpl', $this->getCacheId('belvgcustomhtml_displaycenterhome'));
	}	
	
	public function hookDisplayTopHomeRight()
	{
		if (!$this->isCached('belvgcustomhtml_displaytophomeright.tpl', $this->getCacheId('belvgcustomhtml_displaytophomeright')))
		{
			$block_list = $this->getBlockFromHook('displayTopHomeRight');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaytophomeright.tpl', $this->getCacheId('belvgcustomhtml_displaytophomeright'));
	}
	public function hookDisplayBottomHome()
	{
		if (!$this->isCached('belvgcustomhtml_displaybottomhome.tpl', $this->getCacheId('belvgcustomhtml_displaybottomhome')))
		{
			$block_list = $this->getBlockFromHook('displayBottomHome');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaybottomhome.tpl', $this->getCacheId('belvgcustomhtml_displaybottomhome'));
	}
	public function hookDisplayBannerCategory()
	{
		if (!$this->isCached('belvgcustomhtml_displaybannercategory.tpl', $this->getCacheId('belvgcustomhtml_displaybannercategory')))
		{
			$block_list = $this->getBlockFromHook('displayBannerCategory');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaybannercategory.tpl', $this->getCacheId('belvgcustomhtml_displaybannercategory'));
	}
	public function hookDisplayAfterMenu()
	{
		if (!$this->isCached('belvgcustomhtml_displayaftermenu.tpl', $this->getCacheId('belvgcustomhtml_displayaftermenu')))
		{
			$block_list = $this->getBlockFromHook('displayAfterMenu');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displayaftermenu.tpl', $this->getCacheId('belvgcustomhtml_displayaftermenu'));
	}

	
	public function hookDisplayBottomFooter()
	{
		if (!$this->isCached('belvgcustomhtml_displaybottomfooter.tpl', $this->getCacheId('belvgcustomhtml_displaybottomfooter')))
		{
			$block_list = $this->getBlockFromHook('displayBottomFooter');
			$this->context->smarty->assign(array(
				'block_list' => $block_list,
				'page_name' => $this->context->controller->php_self
			));
		}
		return $this->display(__FILE__, 'belvgcustomhtml_displaybottomfooter.tpl', $this->getCacheId('belvgcustomhtml_displaybottomfooter'));
	}
	public function hookHeader($params)
	{
		$this->context->controller->addCSS($this->_path.'views/css/belvgcustomhtml.css');
	}
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'belvgcustomhtml_shop (id_belvgcustomhtml, id_shop, active)
		SELECT id_belvgcustomhtml, '.(int)$params['new_id_shop'].', active
		FROM '._DB_PREFIX_.'belvgcustomhtml_shop
		WHERE id_shop = '.(int)$params['old_id_shop']);
		
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'belvgcustomhtml_lang (id_belvgcustomhtml, id_lang, id_shop, title, content)
		SELECT id_belvgcustomhtml, id_lang, '.(int)$params['new_id_shop'].', title, content
		FROM '._DB_PREFIX_.'belvgcustomhtml_lang
		WHERE id_shop = '.(int)$params['old_id_shop']);
	}
	public function hookActionObjectLanguageAddAfter($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'belvgcustomhtml_lang (id_belvgcustomhtml, id_lang, id_shop, title, content)
		SELECT id_belvgcustomhtml, '.(int)$params['object']->id.', id_shop, title, content
		FROM '._DB_PREFIX_.'belvgcustomhtml_lang
		WHERE id_lang = '.(int)Configuration::get('PS_LANG_DEFAULT'));	
	}
}
?>
