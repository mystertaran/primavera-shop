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

/** @var {boolean} shopIs177 */
/** @var {Array} inPostLockerCarrierServices */
/** @var {Array} inPostLockerServices */
/** @var {String} inPostLockerStandard */
/** @var {String} inPostLockerEconomy */
/** @var {String|null} inPostGeoWidgetToken */
/** @var {String|null} inPostLanguage */
$(document).ready(function () {
  const $createShipmentModal = $('#inpost-create-shipment-modal');
  const $shipmentForm = $('#inpost-shipment-form');
  const $submitShipmentButtons = $('.js-submit-shipment-form-btn');

  $createShipmentModal.on('shown.bs.modal', function() {
    $(document).off('focusin.modal');
  });

  $(document).on('click', '.js-submit-shipment-form', (e) => {
    e.preventDefault();

    submitShipmentForm(
      () => $createShipmentModal.modal('hide'),
      (response) => {
        if (!('shipment' in response)) {
          return;
        }

        $shipmentForm.find('.form-wrapper').remove();
        $submitShipmentButtons.remove();
        $createShipmentModal.on('hidden.bs.modal', () => window.location.reload());
      }
    );
  });

  $(document).on('click', '.js-submit-shipment-form-and-print-label', (e) => {
    e.preventDefault();

    const $link = $(e.currentTarget);

    function onShipmentCreated(response) {
      const url = new URL($link.attr('href'));
      url.searchParams.set('id_shipment', response.shipment.id);

      $createShipmentModal.modal('hide');
      openPrintLabelModal(url.toString());

      $(document)
        .off('click', '.js-submit-print-label-form')
        .on('click', '.js-submit-print-label-form', (e) => {
          e.preventDefault();

          submitPrintLabelForm((response) => {
            if ('errors' in response) {
              showErrorMessage(response.errors.join('<br/>'));
            }
          });
        });
    }

    submitShipmentForm(onShipmentCreated, (response) => {
      if (!('shipment' in response)) {
        return;
      }

      onShipmentCreated(response);
    });
  });

  function submitShipmentForm(callbackSuccess, callbackError) {
    const formData = new FormData($shipmentForm.get(0));
    formData.append('ajax', '1');
    $submitShipmentButtons.addClass('disabled');

    inPostShippingXhr({
      type: 'POST',
      url: $shipmentForm.attr('action'),
      data: formData,
      callbackJson: function (response) {
        if ('shipment' in response) {
          addShipmentRow(response.shipment);
        }

        if (response.status === true) {
          'function' !== typeof callbackSuccess || callbackSuccess(response);
        } else {
          showFormErrors(response);
          'function' !== typeof callbackError || callbackError(response);
        }

        $submitShipmentButtons.removeClass('disabled');
      },
    });
  }

  function addShipmentRow(shipment) {
    const $row = $(
      $('#js_inpost_shipping_shipment_table_row_template')
        .html()
        .replace('__id__', shipment.id)
        .replace('__service__', shipment.service)
        .replace('__tracking_number__', shipment.tracking_number)
        .replace('__price__', shipment.price)
        .replace('__date_add__', shipment.date_add)
        .replace('__view_url__', shipment.viewUrl)
        .replace('__status_title__', shipment.status.title)
        .replace('__status_description__', shipment.status.description)
    );

    $('.js-inpost-shipping-shipments-table').append($row);

    const $actionContainer = $row.find('.js-inpost-shipping-shipment-actions');

    for (const name in shipment.actions) {
      const action = shipment.actions[name];

      $actionContainer.append(
        $('#js_inpost_shipping_shipment_table_action_template')
          .html()
          .replace('__name__', name)
          .replace('__url__', action.url)
          .replace('__icon__', action.icon)
          .replace('__title__', action.text)
          .replace('__id__', shipment.id)
      );
    }
  }

  function showFormErrors(response) {
    if (!('errors' in response)) {
      return;
    }

    const $errorsWrapper = $('#inpost-shipment-form-errors');
    const errors = response.errors.join('</li><li>');

    $errorsWrapper.html(`<article class="alert alert-danger"><ul><li>${errors}</li></ul></article>`);
    scrollElementIntoView($errorsWrapper);
  }

  $(document).on('click', '.js-submit-dispatch-order-form', function (e) {
    e.preventDefault();

    submitDispatchOrderForm();
  });

  $(document).on('click', '.js-printDispatchOrder', function(e) {
    e.preventDefault();

    inPostShippingXhr({
      url: $(this).attr('href'),
      callbackBlob: blobFileDownload,
      callbackJson: function (response) {
        if ('errors' in response) {
          showErrorMessage(response.errors.join('<br/>'));
        }
      },
    });
  });

  $(document).on('click', '.js-submit-print-label-form', function (e) {
    e.preventDefault();

    submitPrintLabelForm(function (response) {
      if ('errors' in response) {
        showErrorMessage(response.errors.join('<br/>'));
      }
    });
  });

  let id_shipment = null;
  $(document).on('click', '.js-view-inpost-shipment-details', function (e) {
    e.preventDefault();

    const dataId = $(this).data('id-shipment');

    if (id_shipment !== dataId) {
      $.ajax({
        url: $(this).attr('href'),
        dataType: 'json',
        success: function (response) {
          if (response.status === true) {
            id_shipment = dataId;
            const contentWrapper = $('#inpost-shipment-details-content-wrapper');
            contentWrapper.html(response.content);
            if (shopIs177) {
              contentWrapper.find('[data-toggle="pstooltip"]').pstooltip();
            }
            $('#inpost-shipment-details-modal').modal('show');
          } else {
            showErrorMessage(response.errors.join('<br/>'));
          }
        },
      });
    } else {
     $('#inpost-shipment-details-modal').modal('show');
    }
  });

  $(document).on('click', '.js-inpost-new-dispatch-order', function (e) {
    e.preventDefault();

    const url = new URL($(this).attr('href'));
    url.searchParams.set('back', window.location.href);

    window.location.href = url.toString();
  });

  $(document).on('change', '#service', changeShippingService);
  $(document).on('change', '#sending_method', changeSendingMethod);
  $(document).on('change', 'input[name="use_template"]', toggleTemplate);
  $(document).on('change', 'input[name="cod"]', toggleCashOnDelivery);
  $(document).on('change', 'input[name="insurance"]', toggleInsurance);

  changeShippingService();
  toggleCashOnDelivery();

  const lockerAddress = $('.js-inpost-locker-address');

  if (lockerAddress.length > 0) {
    const lockerAddressTarget = shopIs177 ? '#addressShipping' : '#addressShipping .col-sm-6:not(.hidden-print)';
    const carrierNameTarget = shopIs177 ? '#addressInvoice' : lockerAddressTarget;

    lockerAddress.appendTo(lockerAddressTarget).show();
    $('.js-inpost-carrier-name').appendTo(carrierNameTarget).show();
  }

  if ('function' !== typeof InPostShippingGeoWidget) {
    const $openMapButtons = $('.js-inpost-show-map-input');
    $openMapButtons.addClass('disabled');
    $openMapButtons.on('click', (e) => e.preventDefault());
  } else {
    const geoWidget = new InPostShippingGeoWidget(inPostGeoWidgetToken, inPostLanguage);
    const $modal = $('#inpost-shipping-map-modal');
    const $modalContent = $modal.find('.js-inpost-shipping-map-modal-content');

    // Bootstrap 3 removes the "modal-open" class in a listener attached to body
    $(document).on('hidden.bs.modal', '#inpost-shipping-map-modal', () => {
      if ($createShipmentModal.is(':visible')) {
        $('body').addClass('modal-open');
      }
    });

    $(document).on('click', '.js-inpost-show-map-input', function (e) {
      e.preventDefault();

      const inputSelector = $(e.currentTarget).data('target-input');
      const $input = $(inputSelector);

      const isTargetPoint = $input.data('payment');
      const config = getGeoWidgetConfig(isTargetPoint);

      geoWidget.initMap(config, $modalContent, (point) => {
        $input.data('point', point.name);
        $input.val(point.name);

        $modal.modal('hide');
      });

      $modal.modal('show');
    });

    function getGeoWidgetConfig(targetPoint) {
      if (!targetPoint) {
        return 'parcelSend';
      } else if ($('#weekend_delivery_on').is(':checked')) {
        return 'parcelCollect247';
      } else if ($('#cod_on').is(':checked')) {
        return 'parcelCollectPayment';
      } else {
        return 'parcelCollect'
      }
    }
  }

  function toggleTemplate() {
    const useTemplate = '1' === $('input[name="use_template"]:checked').val();

    $('#template').closest('.form-group').toggle(useTemplate);
    $('#js-inpost-package-dimensions').toggle(!useTemplate);
  }

  function toggleCashOnDelivery() {
    $('input[name="cod_amount"]')
      .closest('.form-group')
      .toggle('1' === $('input[name="cod"]:checked').val());

    updateInsuranceDisplay();
  }

  function toggleInsurance() {
    $('input[name="insurance_amount"]')
      .closest('.form-group')
      .toggle('1' === $('input[name="insurance"]:checked').val());
  }

  function updateInsuranceDisplay() {
    if ($('#service').val() !== inPostLockerStandard && $('#cod_on').is(':checked')) {
      $('#insurance_on').prop('checked', true).trigger('change');
      $('input[name="insurance"]').closest('.form-group').hide();
      $('label[for="insurance_amount"]').addClass('required');
    } else {
      $('input[name="insurance"]').closest('.form-group').show();
      $('label[for="insurance_amount"]').removeClass('required');
    }
  }

  function changeShippingService() {
    const service = $('#service').val();
    const isLockerCarrierService = -1 !== inPostLockerCarrierServices.indexOf(service);

    $('#js-inpost-commercial-product-identifier-wrapper').toggle(inPostLockerEconomy === service);
    $('#js-inpost-target-point-wrapper').toggle(-1 !== inPostLockerServices.indexOf(service));
    $('#js-inpost-weekend-delivery-wrapper').toggle(inPostLockerStandard === service);
    $('#js-inpost-dimension-template-content-wrapper').toggle(isLockerCarrierService);

    if (!isLockerCarrierService) {
      $('#template_off').prop('checked', true);
      toggleTemplate();
    }

    updateSendingMethodOptions();
    updateInsuranceDisplay();
  }

  function updateSendingMethodOptions() {
    const selectedService = $('#service option:selected');
    const availableSendingMethods = selectedService.data('sending-methods');

    $('#sending_method option').each(function () {
      const disable = availableSendingMethods.indexOf($(this).val()) === -1;
      $(this)
        .prop('disabled', disable)
        .prop('hidden', disable);
    });

    if ($('#sending_method option:selected').is(':disabled')) {
      const defaultSendingMethod = selectedService.data('default-sending-method');
      if (defaultSendingMethod) {
        $(`#sending_method option[value="${defaultSendingMethod}"]`).prop('selected', true);
      } else {
        $('#sending_method option:not(:disabled):first').prop('selected', true);
      }
    }

    changeSendingMethod();
  }

  function updateTemplateOptions() {
    const unavailableTemplates = $('#sending_method option:selected').data('unavailable-templates') || [];
    const selectedService = $('#service option:selected');
    let availableTemplates = selectedService.data('templates') || [];
    if (unavailableTemplates.length) {
      availableTemplates = availableTemplates.filter(function (template) {
        return unavailableTemplates.indexOf(template) === -1
      });
    }

    $('#template option').each(function () {
      const disable = availableTemplates.indexOf($(this).val()) === -1;
      $(this)
        .prop('disabled', disable)
        .prop('hidden', disable);
    });

    if ($('#template option:selected').is(':disabled')) {
      const defaultTemplate = selectedService.data('default-template');
      if (defaultTemplate && unavailableTemplates.indexOf(defaultTemplate) === -1) {
        $(`#template option[value="${defaultTemplate}"]`).prop('selected', true);
      } else {
        $('#template option:not(:disabled):first').prop('selected', true);
      }
    }
  }

  function changeSendingMethod() {
    const popGroup = $('#dropoff_pop').closest('.form-group');
    const lockerGroup = $('#dropoff_locker').closest('.form-group');
    const sendingMethod = $('#sending_method').val();

    popGroup.toggle('pop' === sendingMethod && -1 !== inPostLockerCarrierServices.indexOf($('#service').val()));
    lockerGroup.toggle('parcel_locker' === sendingMethod);

    updateTemplateOptions();
  }
});
