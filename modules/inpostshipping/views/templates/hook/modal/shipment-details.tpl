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
<div class="panel-body">
  <div class="form-horizontal">
    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Reference' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">{$shipment.reference|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Shipment number' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">{$shipment.tracking_number|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Created at' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">{$shipment.date_add|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='State' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">
          {if $shipment.status.description}
            <a data-toggle="tooltip" title="{$shipment.status.description|escape:'html':'UTF-8'}">
              {$shipment.status.title|escape:'html':'UTF-8'}
            </a>
          {else}
            {$shipment.status.title|escape:'html':'UTF-8'}
          {/if}
        </p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Sending method' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">
          {$shipment.sending_method|escape:'html':'UTF-8'}
        </p>
      </div>
    </div>

    {if $shipment.sending_point}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Sending point' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">
            {$shipment.sending_point|escape:'html':'UTF-8'}
          </p>
        </div>
      </div>
    {/if}

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='InPost Service' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">
          {$shipment.service|escape:'html':'UTF-8'}
          {if $shipment.weekend_delivery}
            - {l s='Weekend delivery' mod='inpostshipping'}
          {/if}
        </p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Receiver email' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">{$shipment.email|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    <div class="form-group">
      <label class="col-lg-3 control-label">{l s='Receiver phone' mod='inpostshipping'}</label>
      <div class="col-lg-9">
        <p class="form-control-static">{$shipment.phone|escape:'html':'UTF-8'}</p>
      </div>
    </div>

    {if $shipment.target_point}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Target point' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">
            {$shipment.target_point|escape:'html':'UTF-8'}
          </p>
        </div>
      </div>
    {/if}

    {if $shipment.template}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Dimension template' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">{$shipment.template|escape:'html':'UTF-8'}</p>
        </div>
      </div>
    {else}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Dimensions' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">
            {$shipment.dimensions.length|floatval} x {$shipment.dimensions.width|floatval} x {$shipment.dimensions.height|floatval} mm
          </p>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Weight' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">{$shipment.dimensions.weight|floatval} kg</p>
        </div>
      </div>
    {/if}

    {if $shipment.cod_amount}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Cash on delivery amount' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">{$shipment.cod_amount|escape:'html':'UTF-8'}</p>
        </div>
      </div>
    {/if}

    {if $shipment.insurance_amount}
      <div class="form-group">
        <label class="col-lg-3 control-label">{l s='Insurance amount' mod='inpostshipping'}</label>
        <div class="col-lg-9">
          <p class="form-control-static">{$shipment.insurance_amount|escape:'html':'UTF-8'}</p>
        </div>
      </div>
    {/if}
  </div>
</div>
