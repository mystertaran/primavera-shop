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
<div class="tab tab-pane" id="inpostshipping">
  <div class="card card-details mb-0">
    <div class="card-header d-none d-print-block">
      {l s='InPost Shipments' mod='inpostshipping'} (<span class="count">{$inPostShipments|count}</span>)
    </div>
    <div class="card-body">
      <div class="form-horizontal">
        <div class="table-responsive">
          <table class="table">
            <thead>
            <tr>
              <th>{l s='Service' mod='inpostshipping'}</th>
              <th>{l s='Shipment number' mod='inpostshipping'}</th>
              <th>{l s='State' mod='inpostshipping'}</th>
              <th class="text-right">{l s='Price' mod='inpostshipping'}</th>
              <th>{l s='Created at' mod='inpostshipping'}</th>
              <th class="text-right product_actions d-print-none">
                {l s='Actions' mod='inpostshipping'}
              </th>
            </tr>
            </thead>
            <tbody class="js-inpost-shipping-shipments-table">
            {foreach $inPostShipments as $shipment}
              {include 'module:inpostshipping/views/templates/hook/177/_partials/shipment-table-row.tpl'}
            {/foreach}
            </tbody>
          </table>

          <div class="row no-gutters d-print-none">
            <div class="col-sm-12 text-right">
              <a class="btn btn-secondary" href="{$inPostShipmentsListUrl|escape:'html':'UTF-8'}">
                {l s='Go to shipments list' mod='inpostshipping'}
              </a>
              <button class="btn btn-primary" data-toggle="modal" data-target="#inpost-create-shipment-modal">
                {l s='New shipment' mod='inpostshipping'}
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {if isset($inPostLockerAddress)}
    <div class="js-inpost-locker-address inpost-locker-address" style="display: none">
      {$inPostLockerAddress}
      <div class="my-3 d-xl-none">{$carrierName}</div>
    </div>

    <div class="js-inpost-carrier-name inpost-locker-address d-none d-xl-block" style="display: none">
      {$carrierName}
    </div>
  {/if}

  <template id="js_inpost_shipping_shipment_table_row_template">
    {include 'module:inpostshipping/views/templates/hook/177/_partials/shipment-table-row.tpl' shipment=null}
  </template>

  <template id="js_inpost_shipping_shipment_table_action_template">
    {include 'module:inpostshipping/views/templates/hook/177/_partials/shipment-table-action.tpl' action_name=null action=null}
  </template>
</div>
