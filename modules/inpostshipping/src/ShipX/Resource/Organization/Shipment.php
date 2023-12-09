<?php
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

namespace InPost\Shipping\ShipX\Resource\Organization;

use InPost\Shipping\Api\Resource\Traits\CreateTrait;
use InPost\Shipping\Api\Resource\Traits\DeleteTrait;
use InPost\Shipping\Api\Resource\Traits\GetTrait;
use InPost\Shipping\ShipX\Resource\Traits\GetCollectionTrait;

/**
 * @property int $id
 * @property string $status
 * @property array $custom_attributes
 * @property array $parcels
 * @property array $sender
 * @property array $receiver
 * @property array $cod
 * @property array $insurance
 * @property string $reference
 * @property string $comments
 * @property bool $is_return
 * @property string $service
 * @property string|null $commercial_product_identifier
 * @property string $tracking_number
 * @property string $sending_method
 * @property string $external_customer_id
 * @property array $offers
 * @property array $selected_offer
 * @property array $transactions
 * @property bool $end_of_week_collection
 */
class Shipment extends OrganizationResource
{
    use GetCollectionTrait;
    use GetTrait;
    use CreateTrait;
    use DeleteTrait;

    const BASE_PATH = '/v1/organizations/{organization_id}/shipments';

    const TYPE_NORMAL = 'normal';
    const TYPE_A6 = 'A6';

    const FORMAT_PDF = 'pdf';
    const FORMAT_EPL = 'epl';
    const FORMAT_ZPL = 'zpl';

    const TEMPLATE_SMALL = 'small';
    const TEMPLATE_MEDIUM = 'medium';
    const TEMPLATE_LARGE = 'large';
    const TEMPLATE_EXTRA_LARGE = 'xlarge';
    const TEMPLATE_PARCEL = 'parcel';
    const TEMPLATE_PALETTE = 'palette';

    const LABEL_FORMATS = [
        self::FORMAT_PDF,
        self::FORMAT_EPL,
        self::FORMAT_ZPL,
    ];

    const LABEL_TYPES = [
        self::TYPE_A6,
        self::TYPE_NORMAL,
    ];

    const DIMENSION_TEMPLATES = [
        self::TEMPLATE_SMALL,
        self::TEMPLATE_MEDIUM,
        self::TEMPLATE_LARGE,
        self::TEMPLATE_EXTRA_LARGE,
        self::TEMPLATE_PARCEL,
        self::TEMPLATE_PALETTE,
    ];

    public static function getLabel($id, array $options = [])
    {
        $path = self::getResourcePath() . '/label';

        return self::cast([])
            ->getRequestFactory()
            ->createRequest('GET', $path, $options)
            ->setPathParams([self::getIdField() => $id])
            ->send();
    }

    public static function getReturnLabel($id, array $options = [])
    {
        return self::getMultipleReturnLabels([$id], $options);
    }

    public static function getMultipleLabels(array $ids, array $options = [])
    {
        $path = self::BASE_PATH . '/labels';

        return self::cast([])
            ->getRequestFactory()
            ->createRequest('GET', $path, $options)
            ->setQueryParams(['shipment_ids' => $ids])
            ->send();
    }

    public static function getMultipleReturnLabels(array $ids, array $options = [])
    {
        $path = self::BASE_PATH . '/return_labels';

        return self::cast([])
            ->getRequestFactory()
            ->createRequest('GET', $path, $options)
            ->setQueryParams(['shipment_ids' => $ids])
            ->send();
    }

    public static function calculatePrices(array $shipments)
    {
        $path = self::BASE_PATH . '/calculate';

        return self::cast([])
            ->getRequestFactory()
            ->createRequest('POST', $path)
            ->setJson(['shipments' => $shipments])
            ->send()
            ->json();
    }
}
