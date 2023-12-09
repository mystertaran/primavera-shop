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
<div id="inpost-product-template" class="panel product-tab">
  <h3>{l s='Default InPost shipment dimension template' mod='inpostshipping'}</h3>

  <div class="form-group">
    <label class="control-label col-lg-3" for="inpost-dimension-template">
			<span class="label-tooltip"
            data-toggle="tooltip"
            title="{l s='Templates for parcel locker shipments will be automatically filled based on the largest template selected for the ordered products' mod='inpostshipping'}"
      >
				{l s='Dimension template' mod='inpostshipping'}
			</span>
    </label>

    <div class="col-lg-5">
      <select id="inpost-dimension-template" name="inpost_dimension_template">
        <option value=""{if !$selectedTemplate} selected="selected"{/if}>---</option>
        {foreach $templateChoices as $choice}
          <option value="{$choice.value|escape:'html':'UTF-8'}"
                  {if $selectedTemplate === $choice.value}selected="selected"{/if}
          >
            {$choice.text|escape:'html':'UTF-8'}
          </option>
        {/foreach}
      </select>
    </div>
  </div>

  <div class="panel-footer">
    <a href="{$link->getAdminLink('AdminProducts')|escape:'html':'UTF-8'}" class="btn btn-default">
      <i class="process-icon-cancel"></i>
      {l s='Cancel'}
    </a>
    <button type="submit" name="submitAddproduct" class="btn btn-default pull-right">
      <i class="process-icon-save"></i>
      {l s='Save'}
    </button>
    <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right">
      <i class="process-icon-save"></i>
        {l s='Save and stay'}
    </button>
  </div>
</div>