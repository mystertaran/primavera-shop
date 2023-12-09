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
  $('.bulk-actions a').each(function () {
    const that = $(this);

    const actionRegex = /sendBulkAction\(.*, ['"]submit(.*)inpost_shipment['"]\)/;
    const matches = actionRegex.exec(that.attr('onclick'));
    if (matches != null && matches[1]) {
      that.removeAttr('onclick');
      that.addClass('js-bulk-' + matches[1].replace('Bulk', ''));
      that.attr('data-action', matches[1]);
    }
  });

  $(document).on('click', '.js-bulk-printLabels', function (e) {
    e.preventDefault();

    openPrintLabelModal(
      getBulkActionUrl($(this).data('action'))
    );
  });

  $(document).on('click', '.js-bulk-printReturnLabels', function (e) {
    e.preventDefault();

    openPrintLabelModal(
      getBulkActionUrl($(this).data('action')),
      false
    );
  });

  $(document).on('click', '.js-bulk-printDispatchOrders', function(e) {
    e.preventDefault();

    inPostShippingXhr({
      type: 'POST',
      url: getBulkActionUrl($(this).data('action')),
      data: getShipmentBoxData(),
      callbackBlob: blobFileDownload,
      callbackJson: function (response) {
        if ('errors' in response) {
          displayAjaxErrors(response.errors);
        }
      },
    });
  });

  $(document).on('click', '.js-bulk-createDispatchOrders', function (e) {
    e.preventDefault();

    openDispatchOrderModal(
      getBulkActionUrl($(this).data('action'))
    );
  });

  $(document).on('click', '.js-submit-dispatch-order-form', function (e) {
    e.preventDefault();

    const formData = $('#inpost-dispatch-order-form').attr('action').indexOf('Bulk') !== -1
      ? getShipmentBoxData()
      : null;

    submitDispatchOrderForm(formData);
  });

  $(document).on('click', '.js-printDispatchOrder', function(e) {
    e.preventDefault();

    inPostShippingXhr({
      type: 'GET',
      url: $(this).attr('href'),
      callbackBlob: blobFileDownload,
      callbackJson: function (response) {
        if ('errors' in response) {
          displayAjaxErrors(response.errors);
        }
      },
    });
  });

  $(document).on('click', '.js-submit-print-label-form', function (e) {
    e.preventDefault();

    const formData = $('#inpost-print-shipment-label-form').attr('action').indexOf('Bulk') !== -1
      ? getShipmentBoxData()
      : null;

    submitPrintLabelForm(function (response) {
      if ('errors' in response) {
        displayAjaxErrors(response.errors);
        $('#inpost-print-shipment-label-modal').modal('hide');
      }
    }, formData);
  });
});

function getBulkActionUrl(action) {
  const url = new URL(controllerUrl);

  url.searchParams.set('action', action);
  url.searchParams.set('ajax', '1');

  return url.toString();
}

function getShipmentBoxData()
{
  const formData = new FormData();

  $('#form-inpost_shipment input[name="inpost_shipmentBox[]"]:checked').each(function () {
    const input = $(this);
    formData.append(input.attr('name'), input.val());
  });

  return formData;
}
