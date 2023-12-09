<a class="btn tooltip-link js-{$action_name|default:'__name__'|escape:'html':'UTF-8'} dropdown-item"
   href="{$action.url|default:'__url__'|escape:'html':'UTF-8'}"
   data-id-shipment="{if isset($shipment.id)}{$shipment.id|intval}{else}__id__{/if}"
>
  <i class="material-icons">
    {$action.icon|default:'__icon__'|escape:'html':'UTF-8'}
  </i>
  {$action.text|default:'__title__'|escape:'html':'UTF-8'}
</a>
