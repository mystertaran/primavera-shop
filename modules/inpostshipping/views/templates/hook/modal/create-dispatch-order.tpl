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
<form id="inpost-dispatch-order-form"
      class="defaultForm form-horizontal"
      enctype="multipart/form-data"
      action="{$dispatchOrderAction|escape:'html':'UTF-8'}"
      autocomplete="off"
>
  <div class="panel-body">
    <div id="inpost-dispatch-order-form-errors"></div>

    <div class="form-wrapper">
      <input type="hidden" name="id_shipment" value="">

      <div class="form-group">
        <label for="id_dispatch_point" class="control-label col-lg-3">
          {l s='Dispatch point' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <select name="id_dispatch_point" id="id_dispatch_point">
            {foreach $dispatchPointChoices as $choice}
              <option value="{$choice.value|intval}"{if $choice.value == $defaultDispatchPoint} selected="selected"{/if}>
                {$choice.text|escape:'html':'UTF-8'}
              </option>
            {/foreach}
          </select>

          <div class="row-margin-top">
            <a class="btn btn-primary fixed-width-md js-inpost-new-dispatch-order"
               href="{$newDispatchPointUrl|escape:'html':'UTF-8'}"
            >
              {l s='Add new' mod='inpostshipping'}
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
