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
<div class="container">
  <div class="row">
    {block name='hook_footer_before'}
      {hook h='displayFooterBefore'}
    {/block}
  </div>
</div>
<div class="footer-container">
  <div class="container-fluid">
    <div class="row">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
      <div class="contact-info-wrap col-md-5 col-sm-12">
        {block name='payments_method'}
        <div class="block-contact links wrapper">
          <div class="hidden-sm-down">
            <div class="payments-container links wrapper">
              <h4 class="text-uppercase block-contact-title">Metody płatności:</h4>
              <div class="payments-info__content">
                <div class="payments-info__item">
                  <img src="https://primavera.sklep.pl/themes/sellon/assets/img/payments/p24nowa.png" alt="P24Now" />
                </div>
                <div class="payments-info__item">
                  <img src="https://primavera.sklep.pl/themes/sellon/assets/img/payments/paypo.png" alt="PayPo" />
                </div>
                <div class="payments-info__item">
                  <img src="https://primavera.sklep.pl/themes/sellon/assets/img/payments/przelewy24.png" alt="Przelewy24" />
                </div>
              </div>
            </div>
          </div>
        </div>
        {/block}
      </div>
	  <div class="contact-info-wrap col-md-5 col-sm-12">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
	  </div>
    </div>
    <div class="row copyright">
      <div class="col-md-12">
          {block name='copyright_link'}
            <a class="_blank" href="https://sdconcept.pl" target="_blank">
              {l s='%copyright% %year% - Sklep Primavera. Wykonanie: %prestashop%' sprintf=['%prestashop%' => 'SDConcept', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme'}
            </a>
          {/block}
      </div>
    </div>
  </div>
</div>
<a id="scroll-top" href="#header">
	<svg version="1.1" id="Capa_1" width="15px" height="15px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 width="451.847px" height="451.846px" viewBox="0 0 451.847 451.846" style="enable-background:new 0 0 451.847 451.846;"
	 xml:space="preserve">
<g>
	<path d="M248.292,106.406l194.281,194.29c12.365,12.359,12.365,32.391,0,44.744c-12.354,12.354-32.391,12.354-44.744,0
		L225.923,173.529L54.018,345.44c-12.36,12.354-32.395,12.354-44.748,0c-12.359-12.354-12.359-32.391,0-44.75L203.554,106.4
		c6.18-6.174,14.271-9.259,22.369-9.259C234.018,97.141,242.115,100.232,248.292,106.406z"/>
</g>
</svg>
</a>
