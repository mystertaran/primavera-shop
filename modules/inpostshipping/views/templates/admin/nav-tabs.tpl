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
<div class="page-head-tabs" id="head_tabs_inpost">
  <ul class="nav">
    {foreach $navTabs as $tab}
      <li class="nav-item">
        <a href="{$tab.href|escape:'html':'UTF-8'}" class="nav-link tab{if $tab.current} active current{/if}">
          {$tab.name|escape:'html':'UTF-8'}
        </a>
      </li>
    {/foreach}
  </ul>
</div>
