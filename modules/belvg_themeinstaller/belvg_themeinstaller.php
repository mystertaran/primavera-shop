<?php
/**
 * 2007-2013 PrestaShop
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
 *         DISCLAIMER   *
 * *************************************** */
 /* Do not edit or add to this file if you wish to upgrade Prestashop to newer
 * versions in the future.
 * ****************************************************
 *
 *  @author     BELVG (store@belvg.com)
 *  @copyright  http://belvg.com
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class belvg_themeinstaller extends Module
{
	const BCV = 'bcv'; //BEFORE CONFIGURATION VALUE

	protected $_hooks = array();

	protected $_configurations = array(
		'HOMESLIDER_WIDTH' => 1920,
	);

    public function __construct()
    {
        $this->name = 'belvg_themeinstaller';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'belvg.com';
        $this->need_instance = 0;
        $this->module_key = '';

        parent::__construct();

        $this->displayName = $this->l('BelVG Theme Installer');
        $this->description = $this->l('BelVG Theme Installer');
    }

    public function install()
    {
        //homeslider:
		$this->_slidesInstallation();
	    //end homeslider.
		
		$sql = array();

        foreach ($sql as $_sql) {
            Db::getInstance()->Execute($_sql);
        }

        $install = parent::install();

        foreach ($this->_hooks as $hook) {
	        $this->registerHook($hook);
        }

		foreach ($this->_configurations as $configuration => $value) {
			Configuration::updateValue(self::BCV . $configuration, Configuration::get($configuration));
			Configuration::updateValue($configuration, $value);
		}

        return $install;
    }

    public function uninstall()
    {
        $sql = array();
        foreach ($sql as $_sql) {
            Db::getInstance()->Execute($_sql);
        }

        foreach ($this->_hooks as $hook) {
	        $this->unregisterHook($hook);
        }

		foreach (array_keys($this->_configurations) as $configuration) {
			Configuration::updateValue($configuration, Configuration::get(self::BCV . $configuration));
		}

        return parent::uninstall();
    }
    

	protected function _slidesInstallation()
	{
        $mDir = _PS_MODULE_DIR_;
        $images = (array)glob(_PS_MODULE_DIR_ . $this->name . '/ps_imageslider/*');
        $images = array_filter($images);

		if (count($images) > 1) {
			$addedIds = array();
			$shops = Shop::getContextListShopID();
			$langs = Language::getLanguages(false);
			$i = 0;
	        foreach ($images as $image) {
		        if (Tools::strtolower(Tools::substr($image, -3)) == 'php') {
			        continue;
		        }

		        $toPath = _PS_MODULE_DIR_ . 'ps_imageslider/images/' . basename($image);
		        if (!file_exists($toPath)) {
			        if (copy($image, $toPath)) {
				        Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'homeslider_slides` VALUES (NULL, ' . $i++ . ', 1)');
				        $id = Db::getInstance()->Insert_ID();
				        if ($id) {
				        	$addedIds[] = $id;
					        foreach ($shops as $shop) {
						         Db::getInstance()->Execute('
						         	INSERT INTO `' . _DB_PREFIX_ . 'homeslider` VALUES (' . $id . ', ' . $shop . ')
						         ');
					        }

					        foreach ($langs as $lang) {
						         Db::getInstance()->Execute('
						         	INSERT INTO `' . _DB_PREFIX_ . 'homeslider_slides_lang` VALUES (
						         	' . $id . ', 
						         	' . $lang['id_lang'] . ', 
						         	"BelVG ' . $i . '", 
						         	" '. pSQL($this->display($this->name, 'homeslider.tpl'), true) .'",
						         	"belvg", 
						         	"#",
						         	"' . basename($image) . '"
						         	)
						         ');
					        }
				        }
			        } else {

			        }
		        } else {
		        }
	        }

	        if (count($addedIds)) {
		        Db::getInstance()->Execute('
		        	UPDATE `' . _DB_PREFIX_ . 'homeslider_slides` SET `active` = 0
		        	WHERE `id_homeslider_slides` NOT IN (' . implode(', ', $addedIds) . ')
		        ');
	        }
	    }
	}
}
