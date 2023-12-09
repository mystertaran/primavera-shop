{**
 * Copyright 2021-2023 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2023 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 *}
<div class="panel col-lg-12">
  <div class="panel-heading">
    <i class="icon-truck"></i>
    {$moduleDisplayName|escape:'html':'UTF-8'}
  </div>
  <div class="panel-body">
    <div class="btn-group dropup">
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        {l s='For the selected orders' mod='inpostshipping'}
        <span class="caret"></span>
      </button>
      <ul class="dropdown-menu">
        {foreach $bulkActions as $bulkAction}
          <li>
            <a href="#" class="{$bulkAction.class|escape:'html':'UTF-8'}" data-action="{$bulkAction.action|escape:'html':'UTF-8'}">
              <i class="icon-{$bulkAction.icon|escape:'html':'UTF-8'}"></i>
              {$bulkAction.label|escape:'html':'UTF-8'}
            </a>
          </li>
        {/foreach}
      </ul>
    </div>
  </div>
</div>
