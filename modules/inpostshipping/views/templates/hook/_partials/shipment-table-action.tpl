<li>
  <a href="{$action.url|default:'__url__'|escape:'html':'UTF-8'}"
     class="js-{$action_name|default:'__name__'|escape:'html':'UTF-8'}"
     data-id-shipment="{if isset($shipment.id)}{$shipment.id|intval}{else}__id__{/if}"
  >
    <i class="icon-{$action.icon|default:'__icon__'|escape:'html':'UTF-8'}"></i>
    {$action.text|default:'__title__'|escape:'html':'UTF-8'}
  </a>
</li>
