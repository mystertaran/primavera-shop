<div id="_desktop_cart">
  <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
    <div class="header">
      {if $cart.products_count > 0}
        <a rel="nofollow" href="{$cart_url}">
      {/if}
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" width="20px" height="20px" viewBox="0 0 48 48" style="enable-background:new 0 0 48 48;" xml:space="preserve">
<g>
	<g>
		<g>
			<path d="M15.733,20.125c1.104,0,2-0.896,2-2v-7.8C17.733,6.838,20.57,4,24.058,4c3.487,0,6.325,2.838,6.325,6.325v7.8     c0,1.104,0.896,2,2,2c1.104,0,2-0.896,2-2v-7.8C34.383,4.632,29.751,0,24.058,0c-5.692,0-10.324,4.632-10.324,10.325v7.8     C13.733,19.229,14.629,20.125,15.733,20.125z"></path>
			<path d="M47,15.631H36.383v2.494c0,2.206-1.794,4-4,4c-2.205,0-4-1.794-4-4v-2.494h-8.649v2.494c0,2.206-1.794,4-4,4     s-4-1.794-4-4v-2.494H1c-0.552,0-0.893,0.435-0.762,0.971L7.235,45.1C7.658,46.702,9.343,48,11,48h26     c1.658,0,3.342-1.299,3.767-2.9l6.996-28.498C47.893,16.065,47.553,15.631,47,15.631z" ></path>
		</g>
	</g>
</g>
</svg>
        <span class="hidden-sm-down">{l s='' d='Shop.Theme.Checkout'}</span>
        <span class="cart-products-count">({$cart.products_count})</span>
      {if $cart.products_count > 0}
        </a>
      {/if}
    </div>
  </div>
</div>
