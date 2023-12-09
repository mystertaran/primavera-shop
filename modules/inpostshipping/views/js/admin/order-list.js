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
$(document).ready(function () {
  $(document).on('click', '.js-inpost-bulk-create-shipments', function (e) {
    e.preventDefault();

    submitBulkCreateShipments(function (response) {
      if ('redirect' in response) {
        window.location.href = response.redirect;
      }
    });
  });

  $(document).on('click', '.js-inpost-bulk-create-print-shipments', function (e) {
    e.preventDefault();

    openPrintLabelModal($(this).data('action'));
  });

  $(document).on('click', '.js-submit-print-label-form', function (e) {
    e.preventDefault();

    submitBulkCreateShipments(function (response) {
      if ('shipmentIds' in response) {
        const formData = new FormData();
        $(response.shipmentIds).each(function (idx, value) {
          formData.append('inpost_shipmentBox[]', value);
        });

        submitPrintLabelForm(function (response) {
          if ('errors' in response) {
            displayAjaxErrors(response.errors);
          }
        }, formData);
      }
    });
  });

  $(document).on('click', '.js-inpost-bulk-create-dispatch-orders', function (e) {
    e.preventDefault();

    openDispatchOrderModal($(this).data('action'));
  });

  $(document).on('click', '.js-submit-dispatch-order-form', function (e) {
    e.preventDefault();

    submitDispatchOrderForm(getOrderBoxData());
  });

  $(document).on('click', '.js-inpost-bulk-print-dispatch-orders', function(e) {
    e.preventDefault();

    inPostShippingXhr({
      type: 'POST',
      url: $(this).data('action'),
      data: getOrderBoxData(),
      callbackBlob: blobFileDownload,
      callbackJson: function (response) {
        if ('errors' in response) {
          displayAjaxErrors(response.errors);
        }
      },
    });
  });

  $(document).on('click', '.js-inpost-bulk-refresh-shipment-status', function (e) {
    e.preventDefault();

    inPostShippingXhr({
      type: 'POST',
      url: $(this).data('action'),
      data: getOrderBoxData(),
      callbackJson: function (response) {
        if ('errors' in response) {
          displayAjaxErrors(response.errors);
        } else {
          displayAjaxSuccess(response.message);
        }
      },
    });
  });
});

function submitBulkCreateShipments(callback) {
  inPostShippingXhr({
    type: 'POST',
    url: $('.js-inpost-bulk-create-shipments').data('action'),
    data: getOrderBoxData(),
    callbackJson: function (response) {
      if ('errors' in response) {
        displayAjaxErrors(response.errors);
      }

      callback(response);
    },
  });
}

function getOrderBoxData() {
  const formData = new FormData();
  const boxes = shopIs177
    ? $('.js-bulk-action-checkbox:checked')
    : $('#form-order input[name="orderBox[]"]:checked');

  boxes.each(function () {
    formData.append('orderIds[]', $(this).val());
  });

  return formData;
}
