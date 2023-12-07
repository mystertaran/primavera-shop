/**
 * 2007-2016 PrestaShop
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';

$(document).ready(function () {

/*
$('.tab-content .tab-pane').eq(0).addClass('active');
var titles = $('.tab-content .nav-item a');
var tabs = $('#home-page-tabs li');  

    for (var i = 0; i < titles.length - 1; i++) {
      tabs.clone().insertAfter(tabs);
    }
    for (var i = 0; i < titles.length; i++) {
	    var newTabs = $('#home-page-tabs li');  
	    	newTabs.eq(0).find('a').addClass('active');
	    var href = titles.eq(i).attr('href');
	    var text = titles.eq(i).text();
	   		newTabs.eq(i).find('a').attr('href', href).text(text);
    };
*/

var tabs = $('.tab-content .nav-item'); 
	//declare first tab as active
	tabs.eq(0).find('a').addClass('active');
	tabs.eq(0).next().addClass('active');	
 	
		tabs.click(function(){
			//add active class to tab that was clicked
			$(this).find('a').addClass('active');
			$(this).next().addClass('active');
			
			//remove active class from siblings tabs
			$(this).siblings().find('a').removeClass('active');
			$(this).siblings().next().removeClass('active');
		});	

var fixedMenu = $('#header .header-top .position-static');		
var startPos = 	fixedMenu.offset().top + 20;
	$(window).scroll(function(){
/*
		if ($(window).scrollTop()>startPos){
			fixedMenu.addClass('fixed-on-scroll');
		}
		else {
			fixedMenu.removeClass('fixed-on-scroll');
		}
*/		
		$(window).scrollTop()>startPos ? fixedMenu.addClass('fixed-on-scroll') : fixedMenu.removeClass('fixed-on-scroll'); 
        
	});
console.log(startPos);	
	 
 });
 

