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
<div class="inpost-shipping-container col-sm-12 js-inpost-shipping-container" data-carrier-id="{$id_carrier|intval}">
  <div class="row">
    <div class="col-sm-12">
      <div class="js-inpost-shipping-errors"></div>
    </div>
  </div>
  <div class="row">
    {if $lockerService}
      <div class="{if $showInputEmail || $showInputPhone}col-sm-6 col-md-7{else}col-sm-12{/if}">
        <div class="inpost-shipping-machine-info js-inpost-shipping-machine-info {if !isset($locker) || !$locker}hidden{/if}">
          <p class="inpost-shipping-machine-name">
            {l s='Parcel Locker' mod='inpostshipping'}
            <span class="js-inpost-shipping-machine-name">
              {if isset($locker.name)}{$locker.name|escape:'html':'UTF-8'}{/if}
            </span>
          </p>
          <p class="inpost-shipping-machine-address js-inpost-shipping-machine-address">
            {if isset($locker.address.line1) && isset($locker.address.line2)}
              {$locker.address.line1|escape:'html':'UTF-8'}, {$locker.address.line2|escape:'html':'UTF-8'}
            {/if}
          </p>
        </div>

        <div class="form-group mb-0">
          <span class="btn inpost-shipping-button js-inpost-shipping-choose-machine"
                data-geo-widget-config="{$geoWidgetConfig|escape:'html':'UTF-8'}"
                data-locker-selected-text="{l s='Change the selected Parcel Locker' mod='inpostshipping'}"
          >
            {if isset($locker) && $locker}
              {l s='Change the selected Parcel Locker' mod='inpostshipping'}
            {else}
              {l s='Select a Parcel Locker' mod='inpostshipping'}
            {/if}
          </span>
        </div>

        <div class="form-group hidden">
          <input type="text"
                 name="inpost_locker[{$id_carrier|intval}]"
                 value="{if isset($locker)}{$locker.name|escape:'html':'UTF-8'}{/if}"
                 class="js-inpost-shipping-input form-control"
                 data-error-text="{l s='Please select a Parcel Locker' mod='inpostshipping'}"
          >
        </div>
      </div>
    {/if}

    {if $showInputEmail || $showInputPhone}
      <div class="col-sm-6 col-md-5{if !$lockerService} col-md-offset-7 col-sm-offset-6{/if}">
        <div class="inpost-shipping-machine-customer-info js-inpost-shipping-machine-customer-info">
          <p class="inpost-shipping-subheader">
            {l s='Your data' mod='inpostshipping'}:
          </p>
          {if $showEmail}
            <p class="inpost-shipping-customer">
              <span class="inpost-shipping-label">{l s='Email' mod='inpostshipping'}:</span>
              <span class="js-inpost-shipping-customer-info-email" data-empty-text="{l s='same as customer account' mod='inpostshipping'}">
                {if !empty($email)}
                  {$email|escape:'html':'UTF-8'}
                {else}
                  {l s='same as customer account' mod='inpostshipping'}
                {/if}
              </span>
            </p>
          {/if}
          {if $showPhone}
            <p class="inpost-shipping-customer">
              <span class="inpost-shipping-label">{l s='Phone' mod='inpostshipping'}:</span>
              <span class="js-inpost-shipping-customer-info-phone" data-empty-text="{l s='same as in delivery address' mod='inpostshipping'}">
                {if !empty($phone)}
                  {$phone|escape:'html':'UTF-8'}
                {else}
                  {l s='same as in delivery address' mod='inpostshipping'}
                {/if}
              </span>
            </p>
          {/if}
          <p class="inpost-shipping-customer-change-wrapper">
            <a class="inpost-shipping-customer-change js-inpost-shipping-customer-change">
              {l s='change' mod='inpostshipping'}
            </a>
          </p>
        </div>
      </div>
    {/if}
  </div>

  {if $renderHiddenPhoneInput}
    <input type="hidden"
           id="inpost_phone_{$id_carrier|intval}"
           name="inpost_phone"
           value="{$phone|escape:'html':'UTF-8'|default:''}"
           class="form-control js-inpost-shipping-phone-hidden"
    >
  {/if}

  {if $showInputEmail || $showInputPhone}
    <div class="row">
      <div class="col-sm-12">
        <div class="inpost-shipping-customer-change-form"
             {if (isset($errors.email) && $showInputEmail) || (isset($errors.phone) && $showInputPhone)}style="display: block"{/if}
        >
          {if $showInputEmail}
            <div class="form-group {if isset($errors.email)}has-error{/if}">
              <input type="text"
                     id="inpost_email_{$id_carrier|intval}"
                     name="inpost_email"
                     value="{$email|escape:'html':'UTF-8'|default:''}"
                     class="form-control js-inpost-shipping-email"
                     placeholder="{l s='Email' mod='inpostshipping'}"
              >
              {if isset($errors.email)}
                <div class="help-block">
                  <ul>
                    <li class="alert alert-danger">{$errors.email|escape:'html':'UTF-8'}</li>
                  </ul>
                </div>
              {/if}
            </div>
          {/if}

          {if $showInputPhone}
            <div class="form-group {if isset($errors.phone)}has-error{/if}">
              <input type="text"
                     id="inpost_phone_{$id_carrier|intval}"
                     name="inpost_phone"
                     value="{$phone|escape:'html':'UTF-8'|default:''}"
                     class="form-control js-inpost-shipping-phone"
                     placeholder="{l s='Phone' mod='inpostshipping'}"
              >
              {if isset($errors.phone)}
                <div class="help-block">
                  <ul>
                    <li class="alert alert-danger">{$errors.phone|escape:'html':'UTF-8'}</li>
                  </ul>
                </div>
              {/if}
            </div>
          {/if}
          <div class="form-group mb-0">
            <span class="btn btn-primary js-inpost-shipping-customer-form-save-button">
              {l s='Save' mod='inpostshipping'}
            </span>
          </div>
        </div>
      </div>
    </div>
  {/if}
</div>
