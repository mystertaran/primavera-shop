/**
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
import $ from 'jquery';

$(document).ready(function () {
  createProductSpin();
  createInputFile();
  coverImage();
  imageScrollBox();

  prestashop.on('updatedProduct', function (event) {
    createInputFile();
    coverImage();
    if (event && event.product_minimal_quantity) {
      const minimalProductQuantity = parseInt(event.product_minimal_quantity, 10);
      const quantityInputSelector = '#quantity_wanted';
      let quantityInput = $(quantityInputSelector);

      // @see http://www.virtuosoft.eu/code/bootstrap-touchspin/ about Bootstrap TouchSpin
      quantityInput.trigger('touchspin.updatesettings', {min: minimalProductQuantity});
    }
    imageScrollBox();
    $($('.tabs .nav-link.active').attr('href')).addClass('active').removeClass('fade');
    $('.js-product-images-modal').replaceWith(event.product_images_modal);
  });

  function coverImage() {
    $('.js-thumb').on(
      'click',
      (event) => {
        $('.js-modal-product-cover').attr('src',$(event.target).data('image-large-src'));
        $('.selected').removeClass('selected');
        $(event.target).addClass('selected');
        $('.js-qv-product-cover').prop('src', $(event.currentTarget).data('image-large-src'));
      }
    );
  }

  function imageScrollBox()
  {
    if ($('#main .js-qv-product-images li').length > 2) {
      $('#main .js-qv-mask').addClass('scroll');
      $('.scroll-box-arrows').addClass('scroll');
        $('#main .js-qv-mask').scrollbox({
          direction: 'h',
          distance: 113,
          autoPlay: false
        });
        $('.scroll-box-arrows .left').click(function () {
          $('#main .js-qv-mask').trigger('backward');
        });
        $('.scroll-box-arrows .right').click(function () {
          $('#main .js-qv-mask').trigger('forward');
        });
    } else {
      $('#main .js-qv-mask').removeClass('scroll');
      $('.scroll-box-arrows').removeClass('scroll');
    }
  } 

  function createInputFile()
  {
    $('.js-file-input').on('change', (event) => {
      let target, file;

      if ((target = $(event.currentTarget)[0]) && (file = target.files[0])) {
        $(target).prev().text(file.name);
      }
    });
  }

  function createProductSpin()
  {
    let quantityInput = $('#quantity_wanted');
    quantityInput.TouchSpin({
      verticalbuttons: true,
      verticalupclass: 'material-icons touchspin-up',
      verticaldownclass: 'material-icons touchspin-down',
      buttondown_class: 'btn btn-touchspin js-touchspin',
      buttonup_class: 'btn btn-touchspin js-touchspin',
      min: parseInt(quantityInput.attr('min'), 10),
      max: 1000000
    });

    quantityInput.on('change', function (event) {
      let $productRefresh = $('.product-refresh');
      $(event.currentTarget).trigger('touchspin.stopspin');
      $productRefresh.trigger('click', {eventType: 'updatedProductQuantity'});
      event.preventDefault();

      return false;
    });
  }
  
//Owl Slider	
var prevArr = '<svg version="1.1" width="45" height="45" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 477.175 477.175" style="enable-background:new 0 0 477.175 477.175;" xml:space="preserve"><g><path d="M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z"></path></g></svg>';
 
var nextArr = '<svg version="1.1" width="45" height="45" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 477.175 477.175" style="enable-background:new 0 0 477.175 477.175;" xml:space="preserve"><g><path d="M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z"></path></g></svg>';

function insertArrows(o, v){
    o.html(v);
}

jQuery('.slider-products').owlCarousel({
	items: 4,
	navigation: true, 
	pagination: false,
	mouseDrag: false
});

jQuery('.owl-prev', '.owl-next' ).empty();
insertArrows(jQuery('.owl-prev'), prevArr);
insertArrows(jQuery('.owl-next'), nextArr);	

jQuery('.categoryproducts h2').append(jQuery('.categoryproducts .slider-products .owl-controls'));
jQuery('.product-accessories h2').append(jQuery('.product-accessories .slider-products .owl-controls'));  
  
  
});
