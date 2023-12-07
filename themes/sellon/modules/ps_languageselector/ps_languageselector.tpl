{*
* 2007-2017 PrestaShop
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
*  @copyright  2007-2017 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="lang_wrapper">
<div id="lang-toggle">
<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve" width="20px" height="20px">
<g>
	<g>
		<path d="M256,0C114.844,0,0,114.844,0,256s114.844,256,256,256s256-114.844,256-256S397.156,0,256,0z M256,469.333    c-58.116,0-110.82-23.418-149.333-61.238v-34.762c0-10.146,4.542-16.677,9.792-24.24C121.865,341.313,128,332.49,128,320    c0-20.344-18.365-22.406-28.24-23.51c-7.063-0.792-13.729-1.542-17.552-5.365c-2.896-2.896-5.792-8.156-8.854-13.719    c-6.081-11.064-14.324-25.695-30.017-34.656C50.236,131.296,142.837,42.667,256,42.667c5.855,0,11.611,0.414,17.349,0.879    c-1.168,0.767-2.599,1.288-3.557,2.246c-2.073,2.073-3.208,4.917-3.125,7.854c0.094,2.927,1.385,5.698,3.573,7.656    c3.833,3.406,4.573,5.125,4.719,5.125c-0.24,0.51-2.198,3.854-13.115,9.396c-18.021,9.135-38.833,27.833-41.927,47.292    c-1.417,8.833,1.146,17.031,7.208,23.094c2,2,4.708,3.125,7.542,3.125c14.813,0,26.26-5.479,37.333-10.771    C283.365,133.135,294.104,128,309.333,128c41.865,0,74.667,9.375,74.667,21.333c0,4.385-1.365,5.729-1.885,6.229    c-5.24,5.156-23.083,4.823-38.771,4.583c-4.156-0.073-8.406-0.146-12.677-0.146c-14.479,0-18.969-2.115-24.167-4.573    c-6.052-2.854-12.906-6.094-29.167-6.094c-17.583,0-50.26,3.177-71.542,24.458c-17.406,17.396-15.563,38.208-14.354,51.969    c0.281,3.167,0.563,6.167,0.563,8.906c0,21.01,21.469,32,42.667,32c32.604,0,60.792,6.083,64,10.667    c0,11.938,3.552,20.094,6.406,26.635c2.375,5.469,4.26,9.781,4.26,16.031c0,4.833-0.792,5.865-2.927,8.615    c-4.073,5.292-7.74,11.052-7.74,23.385c0,22.448,21.615,47.073,24.073,49.813c2.052,2.271,4.948,3.521,7.927,3.521    c0.885,0,1.771-0.104,2.646-0.333c6.281-1.615,61.354-16.771,61.354-53c0-11.354,3.531-14.417,8.885-19.063    c5.25-4.563,12.448-10.802,12.448-23.604c0-8.552,15.177-30.625,29.24-46.177c1.99-2.198,2.979-5.135,2.719-8.094    c-0.26-2.958-1.74-5.677-4.083-7.49c-8.292-6.427-31.188-27.354-38.854-47.656c4.344,2.271,9.781,5.969,14.104,10.292    c3.552,3.573,8.313,5.281,13.729,5.063c8.639-0.493,18.902-7.319,28.628-15.833c4.975,18.046,7.852,36.956,7.852,56.563    C469.333,373.635,373.635,469.333,256,469.333z" style="fill: rgb(0, 0, 0);"></path>
	</g>
</g>	
</svg>
</div>		
<div class="lang_wrapper__inner">
<div id="_desktop_language_selector">
  <div class="language-selector-wrapper">
    <span class="hidden-md-up">{l s='Language:' d='Shop.Theme'}</span>
    <div class="language-selector dropdown js-dropdown">
      <span class="expand-more hidden-sm-down" data-toggle="dropdown">{$current_language.name_simple}</span>
      <a data-target="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="hidden-sm-down">
        <i class="material-icons expand-more">&#xE313;</i>
      </a>
      <ul class="dropdown-menu hidden-sm-down">
        {foreach from=$languages item=language}
          <li {if $language.id_lang == $current_language.id_lang} class="current" {/if}>
            <a href="{url entity='language' id=$language.id_lang}" class="dropdown-item">{$language.name_simple}</a>
          </li>
        {/foreach}
      </ul>
      <select class="link hidden-md-up">
        {foreach from=$languages item=language}
          <option value="{url entity='language' id=$language.id_lang}"{if $language.id_lang == $current_language.id_lang} selected="selected"{/if}>{$language.name_simple}</option>
        {/foreach}
      </select>
    </div>
  </div>
</div>
