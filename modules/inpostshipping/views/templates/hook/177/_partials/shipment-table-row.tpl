<tr>
  <td>{$shipment.service|default:'__service__'|escape:'html':'UTF-8'}</td>
  <td>
    {if array_key_exists('tracking_number', $shipment|default:[])}
      {$shipment.tracking_number|escape:'html':'UTF-8'}
    {else}
      __tracking_number__
    {/if}
  </td>
  <td>
    <span class="text-primary cursor-pointer"
          data-toggle="pstooltip"
          data-boundary="window"
          data-original-title="{$shipment.status.description|default:'__status_description__'|escape:'html':'UTF-8'}"
    >
      {$shipment.status.title|default:'__status_title__'|escape:'html':'UTF-8'}
    </span>
  </td>
  <td class="text-right">{$shipment.price|default:'__price__'|escape:'html':'UTF-8'}</td>
  <td>{$shipment.date_add|default:'__date_add__'|escape:'html':'UTF-8'}</td>
  <td class="d-print-none action-type">
    <div class="btn-group-action text-right">
      <div class="btn-group">
        <a href="{$shipment.viewUrl|default:'__view_url__'|escape:'html':'UTF-8'}"
           class="btn tooltip-link js-view-inpost-shipment-details"
           data-toggle="pstooltip"
           data-original-title="{l s='Details' mod='inpostshipping'}"
           data-id-shipment="{if isset($shipment.id)}{$shipment.id|intval}{else}__id__{/if}"
        >
          <i class="material-icons">zoom_in</i>
        </a>
        <button class="btn btn-link dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
        <div class="dropdown-menu dropdown-menu-right js-inpost-shipping-shipment-actions">
          {if isset($shipment.actions)}
            {foreach $shipment.actions as $action_name => $action}
              {include 'module:inpostshipping/views/templates/hook/177/_partials/shipment-table-action.tpl'}
            {/foreach}
          {/if}
        </div>
      </div>
    </div>
  </td>
</tr>
