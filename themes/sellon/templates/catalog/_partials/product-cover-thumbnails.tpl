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
<div class="images-container">
  {block name='product_cover'}
    <div class="product-cover">
      <img class="js-qv-product-cover" src="{$product.default_image.bySize.large_default.url}" alt="{$product.default_image.legend}" title="{$product.default_image.legend}" style="width:100%;" itemprop="image">
      <div class="layer hidden-sm-down" data-toggle="modal" data-target="#product-modal">
	      <div class="zoom-in">
       <svg version="1.1" width="20px" height="20px" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 52.966 52.966" style="enable-background:new 0 0 52.966 52.966;" xml:space="preserve">
<g>
	<path d="M28.983,20h-6v-6c0-0.552-0.448-1-1-1s-1,0.448-1,1v6h-6c-0.552,0-1,0.448-1,1s0.448,1,1,1h6v6c0,0.552,0.448,1,1,1
		s1-0.448,1-1v-6h6c0.552,0,1-0.448,1-1S29.535,20,28.983,20z"></path>
	<path d="M51.704,51.273L36.845,35.82c3.79-3.801,6.138-9.041,6.138-14.82c0-11.58-9.42-21-21-21s-21,9.42-21,21s9.42,21,21,21
		c5.083,0,9.748-1.817,13.384-4.832l14.895,15.491c0.196,0.205,0.458,0.307,0.721,0.307c0.25,0,0.499-0.093,0.693-0.279
		C52.074,52.304,52.086,51.671,51.704,51.273z M2.983,21c0-10.477,8.523-19,19-19s19,8.523,19,19s-8.523,19-19,19
		S2.983,31.477,2.983,21z"></path>
</g>
</svg> {l s='Zoom In' d='Shop.Theme.Catalog'}
	      </div>
      </div>
    </div>
  {/block}

  {block name='product_images'}
    <div class="js-qv-mask mask">
      <ul class="product-images js-qv-product-images">
        {foreach from=$product.images item=image}
          <li class="thumb-container">
            <img
              class="thumb js-thumb {if $image.id_image == $product.default_image.id_image} selected {/if}"
              data-image-medium-src="{$image.bySize.medium_default.url}"
              data-image-large-src="{$image.bySize.large_default.url}"
              src="{$image.bySize.home_default.url}"
              alt="{$image.legend}"
              title="{$image.legend}"
              width="100"
              itemprop="image"
            >
          </li>
        {/foreach}
      </ul>
    </div>
  {/block}
</div>
{hook h='displayAfterProductThumbs'}
