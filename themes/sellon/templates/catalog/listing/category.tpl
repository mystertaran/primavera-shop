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
{extends file='catalog/listing/product-list.tpl'}

{block name='product_list_header'}

  <div class="block-category row hidden-sm-down">
  <div id="category-description" class="col-md-7">
	  <h1>{$category.name}</h1>
	  {$category.description nofilter}</div>    
  <img src="{$category.image.large.url}" class="col-md-4" alt="{$category.image.legend}">	  
  </div>
  {block name='category_subcategories'}
    <aside class="hidden-sm-down clearfix">
      {if $subcategories|count}
        <nav class="subcategories">
          <ul>
            {foreach from=$subcategories item="subcategory"}
              <li>
                {block name='category_miniature'}
                  {include file='catalog/_partials/miniatures/category.tpl' category=$subcategory}
                {/block}
              </li>
            {/foreach}
          </ul>
        </nav>
      {/if}
    </aside>
  {/block}

{/block}
