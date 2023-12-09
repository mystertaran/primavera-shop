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
<div class="modal fade" id="{$modal_id|escape:'html':'UTF-8'}" tabindex="-1">
  <div class="modal-dialog {if isset($modal_class)}{$modal_class|escape:'html':'UTF-8'}{/if}">
    <div class="modal-content">
      {if isset($modal_title)}
        <div class="modal-header">
          <h4 class="modal-title">{$modal_title|escape:'html':'UTF-8'}</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
      {/if}

      <div class="modal-body">
        {$modal_content}
      </div>

      {if isset($modal_actions)}
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">{l s='Close' d='Admin.Actions'}</button>
          {foreach $modal_actions as $action}
            {if $action.type == 'link'}
              <a href="{$action.href|escape:'html':'UTF-8'}" class="btn {$action.class|escape:'html':'UTF-8'}">
                {$action.label|escape:'html':'UTF-8'}
              </a>
            {elseif $action.type == 'button'}
              <button type="button" value="{$action.value|escape:'html':'UTF-8'}" class="btn {$action.class|escape:'html':'UTF-8'}">
                {$action.label|escape:'html':'UTF-8'}
              </button>
            {/if}
          {/foreach}
        </div>
      {/if}
    </div>
  </div>
</div>
