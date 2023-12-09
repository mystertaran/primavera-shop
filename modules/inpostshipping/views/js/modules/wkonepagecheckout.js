/**
 * Copyright 2021-2023 InPost S.A.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the EUPL-1.2 or later.
 * You may not use this work except in compliance with the Licence.
 *
 * You may obtain a copy of the Licence at:
 * https://joinup.ec.europa.eu/software/page/eupl
 * It is also bundled with this package in the file LICENSE.txt
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the Licence is distributed on an AS IS basis,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the Licence for the specific language governing permissions
 * and limitations under the Licence.
 *
 * @author    InPost S.A.
 * @copyright 2021-2023 InPost S.A.
 * @license   https://joinup.ec.europa.eu/software/page/eupl
 */
$(() => {
  if (0 === $('#wk-one-page-checkout').length) {
    return;
  }

  $('#payment-confirmation button').on('click', (e) => {
    const $selectedDeliveryOption = $('[name^="delivery_option"]:checked');
    if (0 === $selectedDeliveryOption.length) {
      return;
    }

    const selectedCarrierId = parseInt($selectedDeliveryOption.val());
    const $contentWrapper = $(`.js-inpost-shipping-container[data-carrier-id="${selectedCarrierId}"]`);
    if (0 === $contentWrapper.length) {
      return;
    }

    if (!$contentWrapper.is(':visible')) {
      $('.carrier-extra-content').hide();
      $contentWrapper.closest('.carrier-extra-content').slideDown();
    }

    const formData = new FormData();

    formData.append('action', 'updateChoice');
    $contentWrapper.find(':input').each((index, element) => {
      const $input = $(element);
      formData.append($input.attr('name'), $input.val());
    });

    $.ajax({
      async: false,
      method: 'post',
      url: inPostAjaxController,
      data: formData,
      processData: false,
      contentType: false,
      dataType: 'json',
      success: (response) => {
        const $errorsWrapper = $('#wkshipping-error');

        if (false !== response.success) {
          $errorsWrapper.text('');

          return;
        }

        e.stopPropagation();

        const errors = $.map(response.errors, error => `<li>${error}</li>`).join('');

        if (0 < $errorsWrapper.length) {
          $errorsWrapper.html(errors).show();

          $('html, body').animate({
            scrollTop: ($('.wk-shipping-icon').offset().top - 10)
          }, 2000);
        } else if ('function' === typeof wkShowError) {
          wkShowError(errors);
        } else {
          alert($.map(response.errors, error => error).join("\n"));
        }
      }
    });
  });
});
