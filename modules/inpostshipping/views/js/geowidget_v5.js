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
class InPostShippingGeoWidget {
  constructor(token, language = 'pl') {
    this.token = token;
    this.language = language;
  }

  initMap(config, $wrapper, callback = null) {
    const $geoWidget = $wrapper.find('inpost-geowidget');

    if (0 === $geoWidget.length) {
      $wrapper.append(this.createMapElement(config));
    } else if (config !== $geoWidget.attr('config')) {
      $geoWidget.replaceWith(this.createMapElement(config));
    }

    $(document).off('inpostpointselected');
    if ('function' === typeof callback) {
      $(document).on('inpostpointselected', (event) => callback(this.getPoint(event)));
    }
  }

  createMapElement(config) {
    return $(`<inpost-geowidget onpoint="inpostpointselected" token="${this.token}" language="${this.language}" config="${config}"></inpost-geowidget>`);
  }

  getPoint(event) {
    if ('detail' in event) {
      return event.detail;
    } else if ('originalEvent' in event) {
      return this.getPoint(event.originalEvent);
    }

    return null;
  }
}
