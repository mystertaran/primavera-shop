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
* @author    PrestaShop SA <contact@prestashop.com>
* @copyright 2007-2016 PrestaShop SA
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class SampleDataHtml
{
	public function initData($base_url)
	{	
		$content_block1 = '<div class="homepage_banners">
<div class="container-fluid row">
<div class="col-lg-4 col-md-4 col-sm-4 col-sx-12 side-wrapper">
<div class="side lf-side"><a href="#" class="banner-link"><img src="'.$base_url.'themes/sellon/assets/img/hats.jpg" alt="" width="350" height="262" /> </a>
<div class="bann-desc">
<h4>Hats</h4>
<a href="#" class="hiddabl">Shop Now</a></div>
</div>
<div class="side lf-side"><a href="#" class="banner-link"> <img src="'.$base_url.'themes/sellon/assets/img/wallets2.png" alt="" width="350" height="262" /> </a>
<h4>Wallets</h4>
<a href="#" class="hiddabl">Shop Now</a></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-sx-12 center-wrapper">
<div class="center"><a href="#" class="banner-link"> <img src="'.$base_url.'themes/sellon/assets/img/jackets_1.jpg" alt="" width="350" height="550" /> </a>
<h4>Jackets</h4>
<a href="#" class="hiddabl">Shop Now</a></div>
</div>
<div class="col-lg-4 col-md-4 col-sm-4 col-sx-12 side-wrapper">
<div class="side r-side r-sale" style="background: #000; color: #fff;"><a href="#" class="banner-link"> <img src="'.$base_url.'themes/sellon/assets/img/sale.jpg" alt="" width="350" height="262" /> </a>
<div class="banner-descr">
<h1>Sale</h1>
<span>Get Up to 50% Off</span> <a href="#">Shop Now</a></div>
</div>
<div class="side r-side"><a href="#" class="banner-link"> <img src="'.$base_url.'themes/sellon/assets/img/backpack.jpg" alt="" width="350" height="262" /> </a>
<div class="bann-desc">
<h4>Cereals</h4>
<a href="#" class="hiddabl">Shop Now</a></div>
</div>
</div>
<div class="clearfix"></div>
<div class="row container-fluid">
<div class=" bottom-banner col-lg-12 container-fluid"><a href="#" class="banner-link"> <img src="'.$base_url.'themes/sellon/assets/img/bottom-banner1.png" alt="" /></a> 
<div class="banner-desc">
<h2>The Style Room</h2>
<span>See what’s new from contemporary, classic, and designer fashion brands</span> <a class="more-button" href="#">More</a></div>
</div>
</div>
</div>
</div>';		 						 
				  
		$displayHomeFooter = Hook::getIdByName('displayHomeFooter');
		
		$id_shop = Configuration::get('PS_SHOP_DEFAULT');
		
		/*install static Block*/
		$result = true;
		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'belvgcustomhtml` (`id_belvgcustomhtml`, `hook`, `active`) 
			VALUES
			(1, "displayHomeFooter", 1);'); 
		
		$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'belvgcustomhtml_shop` (`id_belvgcustomhtml`, `id_shop`,`active`) 
			VALUES 
			(1,'.$id_shop.', 1);');
		
		foreach (Language::getLanguages(false) as $lang)
		{
			$result &= Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'belvgcustomhtml_lang` (`id_belvgcustomhtml`, `id_shop`, `id_lang`, `title`, `content`) 
			VALUES 
			( "1", "'.$id_shop.'","'.$lang['id_lang'].'","Footer Banners", \''.$content_block1.'\');');
		}
		return $result;
	}
}