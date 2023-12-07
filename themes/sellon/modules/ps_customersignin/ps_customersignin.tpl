<div id="_desktop_user_info">
  <div class="user-info">
    {if $logged}
    <div id="user-toggle">
	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 258.75 258.75" style="enable-background:new 0 0 258.75 258.75;" xml:space="preserve" width="19px" height="19px">
<g>
	<circle cx="129.375" cy="60" r="60"></circle>
	<path d="M129.375,150c-60.061,0-108.75,48.689-108.75,108.75h217.5C238.125,198.689,189.436,150,129.375,150z"></path>
</g>
</svg>
</div>
<div class="logged-wrapper">
      <a
        class="logout hidden-sm-down"
        href="{$logout_url}"
        rel="nofollow"
      >
{*         <i class="material-icons">&#xE7FF;</i> *}
        {l s='Sign out' d='Shop.Theme.Actions'}
      </a>
      <a
        class="account"
        href="{$my_account_url}"
        title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
        rel="nofollow"
      >
{*         <i class="material-icons hidden-md-up logged">&#xE7FF;</i> *}
        <span class="hidden-sm-down">{$customerName}</span>
      </a>
</div>
    {else}
      <a
        href="{$my_account_url}"
        title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
        rel="nofollow"
      >
        	<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Layer_1" x="0px" y="0px" viewBox="0 0 258.75 258.75" style="enable-background:new 0 0 258.75 258.75;" xml:space="preserve" width="20px" height="20px">
<g>
	<circle cx="129.375" cy="60" r="60"></circle>
	<path d="M129.375,150c-60.061,0-108.75,48.689-108.75,108.75h217.5C238.125,198.689,189.436,150,129.375,150z"></path>
</g>
</svg>
{*         <span class="hidden-sm-down">{l s='Sign in' d='Shop.Theme.Actions'}</span> *}
      </a>
    {/if}
  </div>
</div>
