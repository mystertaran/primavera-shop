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
class InPostShippingModalMap {
  constructor() {
    this.type = null;
    this.function = null;
  }

  openMap(config) {
    const options = {
      payment: config.payment || false,
      weekendDelivery: config.weekendDelivery || false,
      pointName: config.pointName || '',
      type: 'parcel_locker_only' === config.type || 'pop' === config.type ? config.type : 'parcel_locker',
      function: 'parcel_send' === config.function ? 'parcel_send' : 'parcel_collect',
    };

    if (options.payment || options.weekendDelivery) {
      options.type = 'parcel_locker_only';
    }

    if (options.type !== this.type || options.function !== this.function) {
      this.type = options.type;
      this.function = options.function;

      const widget = $('#widget-modal');
      if (widget.length) {
        widget.remove();
      }

      const widgetConfig = {
        map: {
          initialTypes: [options.type],
        },
        points: {
          types: [options.type],
          functions: ['parcel', options.function],
        },
        display: {
          showTypesFilters: false,
          showSearchBar: true,
        },
      };

      // if showOnlyWithPayment is set, only points that allow credit card payments are shown on the map
      /*
      if (options.payment) {
        widgetConfig.paymentFilter = {
          showOnlyWithPayment: true,
        };
      }
      */

      easyPack.init(widgetConfig);
    }

    const modal = easyPack.modalMap(function (point, modal) {
      modal.closeModal();

      if (typeof config.callback === 'function') {
        config.callback(point);
      }
    }, {});

    const widget = $('#widget-modal');
    widget.css('max-height', '90%');

    if ('' !== options.pointName) {
      modal.searchLockerPoint(options.pointName);
    }
  }
}
