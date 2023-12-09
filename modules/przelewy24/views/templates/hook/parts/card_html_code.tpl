{*
* @license https://www.gnu.org/licenses/lgpl-3.0.en.html
*}

<div
        id="p24-card-config-element"
        data-ajaxurl="{$p24_charge_card_url|escape:'html':'UTF-8'}"
        data-pagetype="{$p24_page_type|escape:'html':'UTF-8'}"
        data-cartid="{$p24_cart_id|escape:'html':'UTF-8'}"
        data-ids="{$p24_card_ids_string|escape:'html':'UTF-8'}"
></div>

<div style="display: none;">
    <div>
        <div id="p24-card-section" class="p24-inside-section">
            <h1>{l s='Register card and payment' mod='przelewy24'}</h1>
            <form method="post">
                <div>
                    <p>
                        <label>
                            <span>
                                {l s='Name and surname' mod='przelewy24'}
                            </span>
                            <input type="text" name="card-holder">
                        </label>
                    </p>
                    <p>
                        <label>
                            <span>
                                {l s='Card number' mod='przelewy24'}
                            </span>
                            <input type="text" name="card-number">
                        </label>
                    </p>
                    <p>
                        <label>
                                <span>
                                    {l s='Expired date' mod='przelewy24'}
                                </span>
                        </label>
                        <span class="for-date-select">
                        <select name="exp-date-month">
                            <option>01</option>
                            <option>02</option>
                            <option>03</option>
                            <option>04</option>
                            <option>05</option>
                            <option>06</option>
                            <option>07</option>
                            <option>08</option>
                            <option>09</option>
                            <option>10</option>
                            <option>11</option>
                            <option>12</option>
                        </select>
                        <select name="exp-date-year">
                            {foreach $p24_card_years as $one_year}
                                <option>{$one_year}</option>
                            {/foreach}
                        </select>
                        </span>
                    </p>
                    <p>
                        <label>
                            <span>
                                {l s='CVV' mod='przelewy24'}
                            </span>
                            <input type="text" name="cvv" class="short">
                        </label>
                    </p>

                    {if $p24_card_needs_term_accept}
                    <p>
                        <input type="checkbox" name="terms" id="p24-card-regulation-accept" value="1">
                        {l s='Please accept' mod='przelewy24'}
                        <a href="http://www.przelewy24.pl/regulamin.htm" target="_blank">
                            {l s='the Przelewy24 Terms' mod='przelewy24'}
                        </a>
                    </p>
                    {/if}
                    <p class="error error-regulation">{l s='Terms not accepted.' mod='przelewy24'}</p>
                    <p class="error error-pr-regulation">{l s='Terms of this shop not accepted.' mod='przelewy24'}</p>
                    <p class="error error-other">{l s='Invalid card data.' mod='przelewy24'}</p>
                    <p>
                        <button>{l s='Pay by card' mod='przelewy24'}</button>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
