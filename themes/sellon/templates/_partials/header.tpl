{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='header_banner'}
  <div class="header-banner">
    {hook h='displayBanner'}
  </div>
{/block}

{block name='header_nav'}
  <nav class="header-nav">
  <div class="additional-header">
  <div class="contact-info">
    <a href="https://primavera.sklep.pl/kontakt" class="write-to-us">Napisz do nas</a>
    <a href="tel:+48696023073" class="phone">+48 696 023 073</a>
    <span class="opening-hours">Pon - Pt: 9:00 - 17:30, Sob: 9:00 - 13:00</span>
  </div>
  <div class="payment-info">      
    <span>Płatności: Przelewy24, PayPo, P24Now</span>
  </div>
</div>
    <div class="container-fluid">
        <div class="row">
          <div class="hidden-md-up text-xs-center mobile">
            <div class="pull-xs-left" id="menu-icon">
              <i class="material-icons d-inline">&#xE5D2;</i>
            </div>
            <div class="pull-xs-right" id="_mobile_cart"></div>
            <div class="pull-xs-right" id="_mobile_user_info"></div>
            <div class="top-logo" id="_mobile_logo"></div>
            <div class="clearfix"></div>
          </div>
        </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
{hook h='displayColumnsconfigurator'}
    <div class="header-top">
    <div class="container-fluid test-align">
       <div class="row">
        <div class="col-md-12 col-sm-12 position-static">
          <div class="row">
	          <div class="header-top__inner">
	          <div class="col-md-4 header-top__inner_class">  
	          {hook h='displaySearch'}
	          </div>
	        <div class="col-md-4 hidden-sm-down" id="_desktop_logo">
	          <a href="{$urls.base_url}">
	            <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
	          </a>
	        </div>	 
	        <div class="col-md-4 hidden-sm-down" >      
            {hook h='displayCart'}
	        </div>
	          </div>
            {hook h='displayTop'}
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <div id="mobile_top_menu_wrapper" class="row hidden-md-up" style="display:none;">
        <div class="js-top-menu mobile" id="_mobile_top_menu"></div>
        <div class="js-top-menu-bottom">
          <div id="_mobile_currency_selector"></div>
          <div id="_mobile_language_selector"></div>
          <div id="_mobile_contact_link"></div>
        </div>
      </div>
    </div>
  </div>
  {hook h='displayNavFullWidth'}
{/block}
