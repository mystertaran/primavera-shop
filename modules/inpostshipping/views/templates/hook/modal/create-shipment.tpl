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
<form id="inpost-shipment-form"
      class="defaultForm form-horizontal"
      enctype="multipart/form-data"
      action="{$shipmentAction|escape:'html':'UTF-8'}"
      autocomplete="off"
>
  <div class="panel-body">
    <div id="inpost-shipment-form-errors"></div>

    <div class="form-wrapper">
      <input type="hidden" name="id_order" value="{$id_order|intval}">

      <div class="form-group">
        <label for="customer_email" class="control-label col-lg-3">
          {l s='Receiver email' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <input type="text" name="email" id="customer_email" value="{$customerEmail|escape:'html':'UTF-8'}">
        </div>
      </div>

      <div class="form-group">
        <label for="customer_phone" class="control-label col-lg-3">
          {l s='Receiver phone' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <input type="text" name="phone" id="customer_phone" value="{$customerPhone|escape:'html':'UTF-8'}">
        </div>
      </div>

      <div class="form-group">
        <label for="service" class="control-label col-lg-3">
          {l s='Shipping service' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <select name="service" id="service">
            {foreach $serviceChoices as $choice}
              <option value="{$choice.value|escape:'html':'UTF-8'}"
                      {if $choice.disabled}disabled="disabled"{/if}
                      {if $choice.value == $selectedService}selected="selected"{/if}
                      {if isset($defaultTemplates[$choice.value])}
                        data-default-template="{$defaultTemplates[$choice.value]|escape:'html':'UTF-8'}"
                      {/if}
                      {if $choice.availableTemplates}
                        data-templates='["{implode('","', $choice.availableTemplates)|escape:'html':'UTF-8'}"]'
                      {/if}
                      {if isset($defaultSendingMethods[$choice.value])}
                        data-default-sending-method="{$defaultSendingMethods[$choice.value]|escape:'html':'UTF-8'}"
                      {/if}
                      data-sending-methods='["{implode('","', $choice.availableSendingMethods)|escape:'html':'UTF-8'}"]'
              >
                {$choice.text|escape:'html':'UTF-8'}
              </option>
            {/foreach}
          </select>
        </div>
      </div>

      <div id="js-inpost-commercial-product-identifier-wrapper">
        <div class="form-group">
          <label for="commercial_product_identifier" class="control-label col-lg-3">
            {l s='Variant code' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <input type="text"
                   name="commercial_product_identifier"
                   id="commercial_product_identifier"
                   value="{$commercialProductIdentifier|escape:'html':'UTF-8'}"
            >
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="sending_method" class="control-label col-lg-3">
          {l s='Sending method' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <select name="sending_method" id="sending_method">
            {foreach $sendingMethodChoices as $choice}
              <option value="{$choice.value|escape:'html':'UTF-8'}"
                      {if $choice.value == $defaultSendingMethod}selected="selected"{/if}
                      {if $choice.unavailableTemplates}
                        data-unavailable-templates='["{implode('","', $choice.unavailableTemplates)|escape:'html':'UTF-8'}"]'
                      {/if}
              >
                {$choice.text|escape:'html':'UTF-8'}
              </option>
            {/foreach}
          </select>
        </div>
      </div>

      <div class="form-group">
        <label for="dropoff_pop" class="control-label col-lg-3">
            {l s='Sending point' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="text"
                   name="dropoff_pop"
                   id="dropoff_pop"
                   value="{if $defaultPop}{$defaultPop.name|escape:'html':'UTF-8'}{/if}"
                   data-type="pop"
                   data-function="parcel_send"
                   data-point="{if $defaultPop}{$defaultPop.name|escape:'html':'UTF-8'}{/if}"
            >
            <span class="input-group-addon btn btn-default js-inpost-show-map-input" data-target-input="#dropoff_pop">
              {l s='Open map' mod='inpostshipping'}
            </span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="dropoff_locker" class="control-label col-lg-3">
          {l s='Sending point' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="text"
                   name="dropoff_locker"
                   id="dropoff_locker"
                   value="{if $defaultLocker}{$defaultLocker.name|escape:'html':'UTF-8'}{/if}"
                   data-type="parcel_locker"
                   data-function="parcel_send"
                   data-point="{if $defaultLocker}{$defaultLocker.name|escape:'html':'UTF-8'}{/if}"
            >
            <span class="input-group-addon btn btn-default js-inpost-show-map-input" data-target-input="#dropoff_locker">
              {l s='Open map' mod='inpostshipping'}
            </span>
          </div>
        </div>
      </div>

      <div id="js-inpost-target-point-wrapper">
        <div class="form-group">
          <label for="target_point" class="control-label col-lg-3 required">
            {l s='Target point' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <div class="input-group">
              <input type="text"
                     name="target_point"
                     id="target_point"
                     value="{if $selectedPoint}{$selectedPoint|escape:'html':'UTF-8'}{/if}"
                     data-payment="1"
                     data-type="parcel_locker"
                     data-function="parcel_collect"
                     data-point="{if $selectedPoint}{$selectedPoint|escape:'html':'UTF-8'}{/if}"
              >
              <span class="input-group-addon btn btn-default js-inpost-show-map-input" data-target-input="#target_point">
                {l s='Open map' mod='inpostshipping'}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div id="js-inpost-weekend-delivery-wrapper">
        <div class="form-group">
          <label class="control-label col-lg-3">
            {l s='Weekend delivery' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <span class="switch prestashop-switch fixed-width-lg">
              <input type="radio"
                     name="weekend_delivery"
                     id="weekend_delivery_on"
                     value="1"
                     {if $weekendDelivery}checked="checked"{/if}
              >
              <label for="weekend_delivery_on">{l s='Yes' mod='inpostshipping'}</label>
              <input type="radio"
                     name="weekend_delivery"
                     id="weekend_delivery_off"
                     value="0"
                     {if !$weekendDelivery}checked="checked"{/if}
              >
              <label for="weekend_delivery_off">{l s='No' mod='inpostshipping'}</label>
              <a class="slide-button btn"></a>
            </span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="reference" class="control-label col-lg-3">
          {l s='Reference' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <input type="text" name="reference" id="reference" value="{$shipmentReference|escape:'html':'UTF-8'}">
        </div>
      </div>

      <div id="js-inpost-dimension-template-content-wrapper">
        <div class="form-group">
          <label class="control-label col-lg-3">
            {l s='Use a predefined dimension template' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="use_template" id="template_on" value="1"{if $useTemplate} checked="checked"{/if}>
            <label for="template_on">{l s='Yes' mod='inpostshipping'}</label>
            <input type="radio" name="use_template" id="template_off" value="0"{if !$useTemplate} checked="checked"{/if}>
            <label for="template_off">{l s='No' mod='inpostshipping'}</label>
            <a class="slide-button btn"></a>
          </span>
          </div>
        </div>

        <div class="form-group"{if !$useTemplate} style="display: none"{/if}>
          <label for="template" class="control-label col-lg-3">
            {l s='Dimension template' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <select name="template" id="template">
              {foreach $dimensionTemplateChoices as $choice}
                <option value="{$choice.value|escape:'html':'UTF-8'}"{if $choice.value == $template} selected="selected"{/if}>
                  {$choice.text|escape:'html':'UTF-8'}
                </option>
              {/foreach}
            </select>
          </div>
        </div>
      </div>

      <div id="js-inpost-package-dimensions"{if $useTemplate} style="display: none"{/if}>
        <div class="form-group">
          <label for="length" class="control-label col-lg-3 required">
            {l s='Length' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <div class="input-group">
              <input type="text" name="dimensions[length]" id="length" value="{$length|floatval}" class="text-right">
              <span class="input-group-addon fixed-width-xs">mm</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="width" class="control-label col-lg-3 required">
            {l s='Width' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <div class="input-group">
              <input type="text" name="dimensions[width]" id="width" value="{$width|floatval}" class="text-right">
              <span class="input-group-addon fixed-width-xs">mm</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="height" class="control-label col-lg-3 required">
            {l s='Height' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <div class="input-group">
              <input type="text" name="dimensions[height]" id="height" value="{$height|floatval}" class="text-right">
              <span class="input-group-addon fixed-width-xs">mm</span>
            </div>
          </div>
        </div>

        <div class="form-group">
          <label for="weight" class="control-label col-lg-3 required">
            {l s='Weight' mod='inpostshipping'}
          </label>
          <div class="col-lg-9">
            <div class="input-group">
              <input type="text" name="weight" id="weight" value="{$weight|floatval}" class="text-right">
              <span class="input-group-addon fixed-width-xs">kg</span>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-lg-3">
            {l s='Cash on delivery' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="cod" id="cod_on" value="1" {if $cashOnDelivery} checked="checked"{/if}>
            <label for="cod_on">{l s='Yes' mod='inpostshipping'}</label>
            <input type="radio" name="cod" id="cod_off" value="0"{if !$cashOnDelivery} checked="checked"{/if}>
            <label for="cod_off">{l s='No' mod='inpostshipping'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
      </div>

      <div class="form-group">
        <label for="cod_amount" class="control-label col-lg-3">
          {l s='Cash on delivery amount' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="text" name="cod_amount" id="cod_amount" value="{$orderTotal|floatval}" class="text-right">
            <span class="input-group-addon fixed-width-xs">{$currencySign|escape:'html':'UTF-8'}</span>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="control-label col-lg-3">
          {l s='Insurance' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <span class="switch prestashop-switch fixed-width-lg">
            <input type="radio" name="insurance" id="insurance_on" value="1"{if $insurance} checked="checked"{/if}>
            <label for="insurance_on">{l s='Yes' mod='inpostshipping'}</label>
            <input type="radio" name="insurance" id="insurance_off" value="0"{if !$insurance} checked="checked"{/if}>
            <label for="insurance_off">{l s='No' mod='inpostshipping'}</label>
            <a class="slide-button btn"></a>
          </span>
        </div>
      </div>

      <div class="form-group"{if !$insurance} style="display: none"{/if}>
        <label for="insurance_amount" class="control-label col-lg-3">
          {l s='Insurance amount' mod='inpostshipping'}
        </label>
        <div class="col-lg-9">
          <div class="input-group">
            <input type="text" name="insurance_amount" id="insurance_amount" value="{$insuranceAmount|floatval}" class="text-right">
            <span class="input-group-addon fixed-width-xs">{$currencySign|escape:'html':'UTF-8'}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
