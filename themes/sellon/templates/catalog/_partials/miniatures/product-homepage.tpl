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
* @author PrestaShop SA <contact@prestashop.com>
  * @copyright 2007-2017 PrestaShop SA
  * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
  * International Registered Trademark & Property of PrestaShop SA
  *}

  {block name='product_miniature_item'}

  {*
  {assign var = belvg_columns_count value=''}
  {if Module::isEnabled('columnsconfigurator')}
  {assign var = belvg_columns_count_postfix value = Configuration::get('COLUMN_VAL')}
  {assign var = belvg_columns_count value = 'col-'|cat:$belvg_columns_count_postfix}
  {/if}
  *}

  {assign var = belvg_column_value_hp value=''}
  {if Module::isEnabled('columnsconfigurator')}
  {assign var = belvg_column_value_hp_postfix value = Configuration::get('COLUMN_VAL_HP')}
  {assign var = belvg_column_value_hp value = 'col-'|cat:$belvg_column_value_hp_postfix}
  {/if}

  <article class="product-miniature js-product-miniature {$belvg_column_value_hp}"
    data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope
    itemtype="http://schema.org/Product">
    <div class="thumbnail-container">
      {block name='product_thumbnail'}
      <a href="{$product.url}" class="thumbnail product-thumbnail">
        <img src="{$product.cover.bySize.home_default.url}" alt="{$product.cover.legend}"
          data-full-size-image-url="{$product.cover.large.url}">
        {block name='product_price_and_shipping'}
        {if $product.show_price}
        <div class="product-price-and-shipping">
          {if $product.has_discount}
          {hook h='displayProductPriceBlock' product=$product type="old_price"}

          <span class="regular-price">{$product.regular_price}</span>
          {if $product.discount_type === 'percentage'}
          <span class="discount-percentage">{$product.discount_percentage}</span>
          {/if}
          {/if}

          {hook h='displayProductPriceBlock' product=$product type="before_price"}

          <span itemprop="price" class="price">{$product.price}</span>

          {hook h='displayProductPriceBlock' product=$product type='unit_price'}

          {hook h='displayProductPriceBlock' product=$product type='weight'}
        </div>
        {/if}
        {/block}

        {block name='product_flags'}
        <ul class="product-flags">
          {foreach from=$product.flags item=flag}
          <li class="{$flag.type}"><span>{$flag.label}</span></li>
          {/foreach}
        </ul>
        {/block}
      </a>
      {/block}

      <div class="product-description">
        {block name='product_name'}
        <h1 class="h3 product-title" itemprop="name"><a href="{$product.url}">{$product.name|truncate:30:'...'}</a></h1>
        {/block}

        {block name='product_price_and_shipping'}
        {if $product.show_price}
        <div class="product-price-and-shipping">
          {if $product.has_discount}
          {hook h='displayProductPriceBlock' product=$product type="old_price"}

          <span class="regular-price">{$product.regular_price}</span>
          {if $product.discount_type === 'percentage'}
          <span class="discount-percentage">{$product.discount_percentage}</span>
          {/if}
          {/if}

          {hook h='displayProductPriceBlock' product=$product type="before_price"}

          <span itemprop="price" class="price">{$product.price}</span>

          {hook h='displayProductPriceBlock' product=$product type='unit_price'}

          {hook h='displayProductPriceBlock' product=$product type='weight'}
        </div>
        {/if}
        {/block}

        {block name='product_reviews'}
        {hook h='displayProductListReviews' product=$product}
        {/block}
      </div>

      <div class="more-info-btn-wrap">
        <a href="{$product.url}" class="light-button more-info-btn">
          {l s='More Info' d='Shop.Theme'}
        </a>
      </div>


      <div class="highlighted-informations{if !$product.main_variants} no-variants{/if}">
        {block name='quick_view'}
        <div class="quick-view-wrap hidden-sm-down">
          <a class="quick-view" href="#" data-link-action="quickview">
            <i class="material-icons search">&#xE8B6;</i> {l s='Quick view' d='Shop.Theme.Actions'}
          </a>
        </div>
        {/block}

        {block name='product_availability'}
        {if $product.show_availability}
        {* availability may take the values "available" or "unavailable" *}
        <span class='product-availability {$product.availability}'>{$product.availability_message}</span>
        {/if}
        {/block}

        {*
        {block name='product_variants'}
        {if $product.main_variants}
        {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
        {/if}
        {/block}
        *}
      </div>

  </article>
  {*
  {block name='columnsconfigurator'}
  <script type="text/javascript">
    var belvg_column_value = { $column.column_value.value }; 
  </script>
  {/block}
  *}

  {/block}