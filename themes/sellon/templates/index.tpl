{**
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
  * @copyright 2007-2017 PrestaShop SA
  * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
  * International Registered Trademark & Property of PrestaShop SA
  *}
  {extends file='page.tpl'}

  {block name='page_content_container'}
  <section id="content" class="page-home">
    {block name='page_content_top'}{/block}

    {block name='page_content'}
    {block name='hook_home'}

    {block name='about_us_content'}
    <div class="about-us-container">
      <div class="about-us-text-column">
        <div class="about-us-text-content">
          <h2>Primavera Sklep - z pasji do mody</h2>
          <p>Witaj w Primavera – miejscu, gdzie moda staje się wyrazem Twojej indywidualności! Od ponad dwóch dekad, z
            pasją do świata mody, działamy na rynku, przynosząc naszym klientkom najnowsze trendy i niepowtarzalny styl.
            <br><br>
            To, co wyróżnia Primavera, to nie tylko nasze 20 lat doświadczenia, ale również nieustanne dążenie do
            zaspokajania oczekiwań każdej kobiety. Nasza historia sięga głęboko, a w 2023 roku otworzyliśmy drzwi do
            świata Primavera również online, tworząc sklep internetowy.
            <br><br>
            Chcemy być bliżej Ciebie, dostarczając nie tylko ubrań, ale również inspiracji.
            <br><br>
            W Primavera wierzymy, że moda to nie tylko ubrania na wieszakach, ale manifestacja Twojego stylu i
            osobowości.
            Zawsze stawialiśmy na najnowsze trendy, dostosowane do różnorodnych gustów i sylwetek. Nasza oferta jest
            pełna
            unikatowych kreacji, które podkreślają indywidualność każdej kobiety.</br>
            <br><br>
            Dziękujemy, że jesteś z nami w podróży przez świat mody. Przekonaj się, jak Primavera może być Twoim
            partnerem w odkrywaniu własnego stylu – z nami każdy dzień to wyjątkowa okazja do wyrażenia siebie!
          </p>
        </div>
      </div>
      <div class="about-us-image-column">
        <img src="http://primavera.sklep.pl/themes/sellon/assets/img/about-us.jpg" alt="Zdjęcie o nas">
      </div>
    </div>
    {/block}

    <div class="clearfix"></div>
    <div class="tab-content" id="homepage-tabs">
      {*
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
      *}
      {hook h='displayHomeTabs'}
    </div>
    <div class="clearfix"></div>
    {/block}
    {hook h='displayHomeFooter'}
    {/block}
   {* {block name='about_us_content'}
    <div class="about-us-container">
      <div class="about-us-text-column">
        <div class="about-us-text-content">
          <h2>Primavera Sklep - z pasji do mody</h2>
          <p>Witaj w Primavera – miejscu, gdzie moda staje się wyrazem Twojej indywidualności! Od ponad dwóch dekad, z
            pasją do świata mody, działamy na rynku, przynosząc naszym klientkom najnowsze trendy i niepowtarzalny styl.
            <br><br>
            To, co wyróżnia Primavera, to nie tylko nasze 20 lat doświadczenia, ale również nieustanne dążenie do
            zaspokajania oczekiwań każdej kobiety. Nasza historia sięga głęboko, a w 2023 roku otworzyliśmy drzwi do
            świata Primavera również online, tworząc sklep internetowy.
            <br><br>
            Chcemy być bliżej Ciebie, dostarczając nie tylko ubrań, ale również inspiracji.
            <br><br>
            W Primavera wierzymy, że moda to nie tylko ubrania na wieszakach, ale manifestacja Twojego stylu i
            osobowości.
            Zawsze stawialiśmy na najnowsze trendy, dostosowane do różnorodnych gustów i sylwetek. Nasza oferta jest
            pełna
            unikatowych kreacji, które podkreślają indywidualność każdej kobiety.</br>
            <br><br>
            Dziękujemy, że jesteś z nami w podróży przez świat mody. Przekonaj się, jak Primavera może być Twoim
            partnerem w odkrywaniu własnego stylu – z nami każdy dzień to wyjątkowa okazja do wyrażenia siebie!
          </p>
        </div>
      </div>
      <div class="about-us-image-column">
        <img src="http://primavera.sklep.pl/themes/sellon/assets/img/about-us.jpg" alt="Zdjęcie o nas">
      </div>
    </div> *}
    {/block}
  </section>
  {/block}