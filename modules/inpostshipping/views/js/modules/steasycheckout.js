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
  const orderErrorsBlockClass = 'js-inpost-method-error';

  const buildErrorsBlock = (messages) => {
    messages = $.map(messages, (message) => `<li>${message}</li>`).join('');

    return `<div class="${orderErrorsBlockClass}">
      <ul class="alert alert-danger">
        ${messages}
      </ul>
    </div>`;
  }

  const scrollToBlock = ($block) => $('html, body').animate({
    scrollTop: $block.offset().top
  }, 500);

  const showErrors = (messages, $appendTo) => {
    $appendTo.prepend(buildErrorsBlock(messages));
    scrollToBlock($appendTo);
  }

  const eraseErrors = () => {
    $(`.${orderErrorsBlockClass}`).remove();
  }

  $('body').on('click', '.steco_confirmation_btn', (e) => {
    eraseErrors();

    const $contentWrapper = $('.js-inpost-shipping-container:visible');
    const $stPhoneInput = $('.st_address_form_delivery input[name="phone"]');
    const $inpostPhoneInput = $('input[name="inpost_phone"]');

    if ($contentWrapper.length > 0) {
      setHiddenPhoneInputValue($contentWrapper);
      const $stPhoneInput = $('.st_address_form_delivery input[name="phone"]');
      const $inpostPhoneInput = $('input[name="inpost_phone"]');

      const formData = new FormData();

      formData.append('action', 'updateChoice');
      $contentWrapper.find(':input').each((index, element) => {
        const $input = $(element);
        formData.append($input.attr('name'), $input.val());
      });

      if($stPhoneInput.length && !$inpostPhoneInput.val() && $stPhoneInput.val()){
        formData.set('inpost_phone', $stPhoneInput.val());
      }

      $.ajax({
        async: false,
        method: 'post',
        url: inPostAjaxController,
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: (response) => {
          if (false === response.success && response.errors) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            showErrors(response.errors, $contentWrapper);
          }
        }
      });
    }
  });

  const setHiddenPhoneInputValue = ($contentWrapper) => {
    const $input = $contentWrapper.find('.js-inpost-shipping-phone-hidden');
    if (0 === $input.length) {
      return;
    }

    const $addressForm = $('.st_address_form_delivery');
    if (0 === $addressForm.length) {
      $input.val('');

      return;
    }

    const $phoneInputs = $addressForm
        .find('[name="phone_mobile"], [name="phone"]')
        .sort((a, b) => {
          // prioritize "phone_mobile" over "phone"
          return $(a).attr('name') === 'phone_mobile' ? -1 : 1;
        });

    $phoneInputs.each((index, shippingInput) => {
      const phone = $(shippingInput).val();
      if ('' !== phone.trim()) {
        $input.val(phone);

        // RETURN FALSE TO BREAK THE LOOP IF WE FOUND A VALUE
        return false;
      }
    });
  }
});
