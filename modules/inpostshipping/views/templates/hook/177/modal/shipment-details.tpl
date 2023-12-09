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
    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Reference' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">{$shipment.reference|escape:'html':'UTF-8'}</div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Shipment number' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">{$shipment.tracking_number|escape:'html':'UTF-8'}</div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Created at' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">{$shipment.date_add|escape:'html':'UTF-8'}</div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='State' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">
          {if $shipment.status.description}
            <span class="text-primary cursor-pointer"
                  data-toggle="pstooltip"
                  data-original-title="{$shipment.status.description|escape:'html':'UTF-8'}"
            >
              {$shipment.status.title|escape:'html':'UTF-8'}
            </span>
          {else}
            {$shipment.status.title|escape:'html':'UTF-8'}
          {/if}
        </div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Sending method' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">
          {$shipment.sending_method|escape:'html':'UTF-8'}
        </div>
      </div>
    </div>

    {if $shipment.sending_point}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Sending point' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">
            {$shipment.sending_point|escape:'html':'UTF-8'}
          </div>
        </div>
      </div>
    {/if}

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='InPost Service' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">
          {$shipment.service|escape:'html':'UTF-8'}
          {if $shipment.weekend_delivery}
            - {l s='Weekend delivery' mod='inpostshipping'}
          {/if}
        </div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Receiver email' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">{$shipment.email|escape:'html':'UTF-8'}</div>
      </div>
    </div>

    <div class="form-group row">
      <label class="form-control-label font-weight-bold">{l s='Receiver phone' mod='inpostshipping'}</label>
      <div class="col-sm">
        <div class="form-control border-0">{$shipment.phone|escape:'html':'UTF-8'}</div>
      </div>
    </div>

    {if $shipment.target_point}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Target point' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">
            {$shipment.target_point|escape:'html':'UTF-8'}
          </div>
        </div>
      </div>
    {/if}

    {if $shipment.template}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Dimension template' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">{$shipment.template|escape:'html':'UTF-8'}</div>
        </div>
      </div>
    {else}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Dimensions' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">
            {$shipment.dimensions.length|floatval} x {$shipment.dimensions.width|floatval} x {$shipment.dimensions.height|floatval} mm
          </div>
        </div>
      </div>

      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Weight' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">{$shipment.dimensions.weight|floatval} kg</div>
        </div>
      </div>
    {/if}

    {if $shipment.cod_amount}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Cash on delivery amount' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">{$shipment.cod_amount|escape:'html':'UTF-8'}</div>
        </div>
      </div>
    {/if}

    {if $shipment.insurance_amount}
      <div class="form-group row">
        <label class="form-control-label font-weight-bold">{l s='Insurance amount' mod='inpostshipping'}</label>
        <div class="col-sm">
          <div class="form-control border-0">{$shipment.insurance_amount|escape:'html':'UTF-8'}</div>
        </div>
      </div>
    {/if}
  </div>
</div>
