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
<div class="tab-pane" id="inpostshipping">
  <h4 class="visible-print">{l s='InPost Shipments' mod='inpostshipping'}</h4>
  <div class="form-horizontal">
    <div class="table-responsive">
      <table class="table">
        <thead>
        <tr>
          <th>
            <span class="title_box">{l s='Service' mod='inpostshipping'}</span>
          </th>
          <th>
            <span class="title_box">{l s='Shipment number' mod='inpostshipping'}</span>
          </th>
          <th>
            <span class="title_box">{l s='State' mod='inpostshipping'}</span>
          </th>
          <th>
            <span class="title_box">{l s='Price' mod='inpostshipping'}</span>
          </th>
          <th>
            <span class="title_box">{l s='Created at' mod='inpostshipping'}</span>
          </th>
          <th></th>
        </tr>
        </thead>
        <tbody class="js-inpost-shipping-shipments-table">
        {foreach $inPostShipments as $shipment}
          {include file='./_partials/shipment-table-row.tpl'}
        {/foreach}
        </tbody>
      </table>

      <div class="row">
        <a class="btn btn-default" href="{$inPostShipmentsListUrl|escape:'html':'UTF-8'}">
          {l s='Go to shipments list' mod='inpostshipping'}
        </a>
        <button class="btn btn-primary" data-toggle="modal" data-target="#inpost-create-shipment-modal">
          {l s='New shipment' mod='inpostshipping'}
        </button>
      </div>
    </div>
  </div>

  {if isset($inPostLockerAddress)}
    <div class="js-inpost-locker-address inpost-locker-address" style="display: none">
      {$inPostLockerAddress}
    </div>

    <div class="js-inpost-carrier-name inpost-locker-address" style="display: none">
      {$carrierName}
    </div>
  {/if}

  <template id="js_inpost_shipping_shipment_table_row_template">
    {include file='./_partials/shipment-table-row.tpl' shipment=null}
  </template>

  <template id="js_inpost_shipping_shipment_table_action_template">
    {include file='./_partials/shipment-table-action.tpl' action_name=null action=null}
  </template>
</div>
