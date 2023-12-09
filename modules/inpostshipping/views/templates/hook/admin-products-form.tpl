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
<div class="col-md-12">
  <h2>
    {l s='Default InPost shipment dimension template' mod='inpostshipping'}
    <span class="help-box"
          data-toggle="popover"
          data-content="{l s='Templates for parcel locker shipments will be automatically filled based on the largest template selected for the ordered products' mod='inpostshipping'}"
    ></span>
  </h2>
</div>
<div class="col-md-4">
  <fieldset class="form-group">
    <label for="inpost-dimension-template">
      {l s='Dimension template' mod='inpostshipping'}
    </label>

    <select id="inpost-dimension-template"
            name="inpost_dimension_template"
            class="custom-select"
    >
      <option value=""{if !$selectedTemplate} selected="selected"{/if}>---</option>
      {foreach $templateChoices as $choice}
        <option value="{$choice.value|escape:'html':'UTF-8'}"
                {if $selectedTemplate === $choice.value}selected="selected"{/if}
        >
          {$choice.text|escape:'html':'UTF-8'}
        </option>
      {/foreach}
    </select>
  </fieldset>
</div>
