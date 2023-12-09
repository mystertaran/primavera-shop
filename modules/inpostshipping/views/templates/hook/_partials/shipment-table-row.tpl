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
    <a data-toggle="tooltip" title="{$shipment.status.description|default:'__status_description__'|escape:'html':'UTF-8'}">
      {$shipment.status.title|default:'__status_title__'|escape:'html':'UTF-8'}
    </a>
  </td>
  <td class="text-right">{$shipment.price|default:'__price__'|escape:'html':'UTF-8'}</td>
  <td>{$shipment.date_add|default:'__date_add__'|escape:'html':'UTF-8'}</td>
  <td class="text-right">
    <div class="btn-group-action">
      <div class="btn-group pull-right">
        <a href="{$shipment.viewUrl|default:'__view_url__'|escape:'html':'UTF-8'}"
           class="btn btn-default js-view-inpost-shipment-details"
           data-id-shipment="{if isset($shipment.id)}{$shipment.id|intval}{else}__id__{/if}"
        >
          <i class="icon-eye"></i>
          {l s='Details' mod='inpostshipping'}
        </a>

        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">
          <i class="icon-caret-down"></i>&nbsp;
        </button>

        <ul class="dropdown-menu js-inpost-shipping-shipment-actions" role="menu">
          {foreach $shipment.actions as $action_name => $action}
            {include './shipment-table-action.tpl'}
          {/foreach}
        </ul>
      </div>
    </div>
  </td>
</tr>
