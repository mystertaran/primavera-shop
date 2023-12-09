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
<form id="inpost-print-shipment-label-form"
      class="defaultForm form-horizontal"
      enctype="multipart/form-data"
      action=""
>
  <div class="panel-body">
    <div class="form-wrapper">
      <div class="row">
        <div class="col">
          <div class="form-group">
            <label class="form-control-label">
              {l s='Label format' mod='inpostshipping'}
            </label>
            {foreach $labelFormatChoices as $choice}
              <div class="radio">
                <label>
                  <input type="radio"
                         name="label_format"
                         id="label_format_{$choice.value|escape:'html':'UTF-8'}"
                         value="{$choice.value|escape:'html':'UTF-8'}"
                         {if $choice.value === $defaultLabelFormat}checked="checked"{/if}
                  >
                  {$choice.text|escape:'html':'UTF-8'}
                </label>
              </div>
            {/foreach}
          </div>
        </div>

        <div class="col">
          <div class="form-group">
            <label class="form-control-label">
              {l s='Label type' mod='inpostshipping'}
            </label>
            {foreach $labelTypeChoices as $choice}
              <div class="radio">
                <label>
                  <input type="radio"
                         name="label_type"
                         id="label_type_{$choice.value|escape:'html':'UTF-8'}"
                         value="{$choice.value|escape:'html':'UTF-8'}"
                         {if $choice.value === $defaultLabelType}checked="checked"{/if}
                  >
                  {$choice.text|escape:'html':'UTF-8'}
                </label>
              </div>
            {/foreach}
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
