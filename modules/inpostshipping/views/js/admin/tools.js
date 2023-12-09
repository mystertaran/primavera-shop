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
function inPostShippingXhr(config) {
  const options = {
    type: config.type || 'POST',
    url: config.url || window.location.href,
    data: config.data || null,
  }

  const loader = $('.inpost-loader');
  loader.addClass('active');

  const xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.HEADERS_RECEIVED) {
      const contentType = xhr.getResponseHeader('Content-type');
      if (contentType === 'application/json') {
        xhr.responseType = 'json';
      } else if (contentType === 'text/html') {
        xhr.responseType = 'text';
      } else {
        xhr.responseType = 'blob';
      }
    } else if (xhr.readyState === XMLHttpRequest.DONE) {
      loader.removeClass('active');

      if (xhr.status === 200) {
        if (xhr.responseType === 'json') {
          if (typeof config.callbackJson === 'function') {
            config.callbackJson(xhr.response, xhr);
          }
        } else if (xhr.responseType === 'text') {
          if (typeof config.callbackHtml === 'function') {
            config.callbackHtml(xhr.response, xhr);
          }
        } else if (typeof config.callbackBlob === 'function') {
          config.callbackBlob(xhr.response, xhr);
        }
      }
    }
  }

  xhr.open(options.type, options.url, true);
  xhr.send(options.data);
}

function blobFileDownload(data, xhr) {
  let filename = '';
  const disposition = xhr.getResponseHeader('Content-Disposition');
  if (disposition && disposition.indexOf('attachment') !== -1) {
    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
    const matches = filenameRegex.exec(disposition);
    if (matches != null && matches[1]) {
      filename = matches[1].replace(/['"]/g, '');
    }
  }

  const blob = new Blob([data], {
    type: xhr.getResponseHeader('Content-type'),
  });

  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.style.display = 'none';
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  window.URL.revokeObjectURL(url);
  a.remove();
}

function displayAjaxErrors(errors) {
  const ajaxBox = $('#ajaxBox');

  errors = errors.join('</li><li>');
  ajaxBox
    .html(`<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><ul><li>${errors}</li></ul></div>`)
    .show();

  scrollElementIntoView(ajaxBox);
}

function displayAjaxSuccess(message) {
  const ajaxBox = $('#ajaxBox');

  ajaxBox
    .html(`<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>${message}</div>`)
    .show();

  scrollElementIntoView(ajaxBox);
}

function scrollElementIntoView(selector) {
  selector.get(0).scrollIntoView({
    block: 'end',
    behavior: 'smooth',
  });
}

function openPrintLabelModal(action, showType = true) {
  const form = $('#inpost-print-shipment-label-form');

  form.attr('action', action);
  if (showType) {
    form.find('input[name="label_type"]').closest('.form-group').show();
  } else {
    form.find('input[name="label_type"]').closest('.form-group').hide();
  }

  $('#inpost-print-shipment-label-modal').modal('show');
}

function submitPrintLabelForm(callbackJson, formData = null, callbackSuccess = null) {
  const form = $('#inpost-print-shipment-label-form');

  if (formData) {
    form.find('input:checked').each(function () {
      const input = $(this);
      formData.append(input.attr('name'), input.val());
    });
  } else {
    formData = new FormData(form.get(0));
  }

  inPostShippingXhr({
    url: form.attr('action'),
    data: formData,
    callbackBlob: function(data, xhr) {
      blobFileDownload(data, xhr);
      if ('function' === typeof callbackSuccess) {
        callbackSuccess();
      } else {
        $('#inpost-print-shipment-label-modal').modal('hide');
      }
    },
    callbackJson: callbackJson,
  });
}

function openDispatchOrderModal(action) {
  const form = $('#inpost-dispatch-order-form');
  form.attr('action', action);

  $('#inpost-create-dispatch-order-modal').modal('show');
}

function submitDispatchOrderForm(formData = null) {
  const form = $('#inpost-dispatch-order-form');

  if (formData) {
    formData.append('id_dispatch_point', $('#id_dispatch_point').val());
  } else {
    formData = new FormData(form.get(0));
  }

  formData.append('ajax', '1');

  inPostShippingXhr({
    url: form.attr('action'),
    data: formData,
    callbackJson: function (response) {
      if ('errors' in response) {
        const errors = response.errors.join('</li><li>');
        $('#inpost-dispatch-order-form-errors').html(`<article class="alert alert-danger"><ul><li>${errors}</li></ul></article>`);
      } else if ('redirect' in response) {
        window.location.href = response.redirect;
      } else {
        window.location.reload();
      }
    },
  });
}
