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
  if ('undefined' === typeof tc_confirmOrderValidations) {
    return;
  }

  tc_confirmOrderValidations['inpostshipping'] = () => {
    const $contentWrapper = $('.js-inpost-shipping-container:visible');
    if (0 === $contentWrapper.length) {
      return true;
    }

    $('.shipping-validation-details').remove();
    setHiddenPhoneInputValue($contentWrapper);

    let errors = null;
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
        if (false !== response.success) {
          return;
        }

        errors = $.map(response.errors, (error) => `<li>${error}</li>`).join('');
      }
    });

    if (null === errors) {
      return true;
    }

    const $errorWrapper = $('#thecheckout-shipping > .inner-area > .error-msg');

    $errorWrapper.append(`<span class="shipping-validation-details"><ul>${errors}</ul></span>`)
    $errorWrapper.show();

    if ('function' === typeof scrollToElement) {
      scrollToElement($errorWrapper);
    }

    return false;
  };

  const setHiddenPhoneInputValue = ($contentWrapper) => {
    const $input = $contentWrapper.find('.js-inpost-shipping-phone-hidden');
    if (0 === $input.length) {
      return;
    }

    const $deliveryAddressFormWrapper = $('#thecheckout-address-delivery');
    const $addressFormWrapper = $deliveryAddressFormWrapper.is('visible')
      ? $deliveryAddressFormWrapper
      : $('#thecheckout-address-invoice');

    const $phoneInputs = $addressFormWrapper
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
