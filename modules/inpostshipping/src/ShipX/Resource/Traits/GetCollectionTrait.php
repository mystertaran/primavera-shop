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

namespace InPost\Shipping\ShipX\Resource\Traits;

use InPost\Shipping\ShipX\Resource\ShipXCollection;
use InPost\Shipping\ShipX\Resource\ShipXResource;

/**
 * @mixin ShipXResource
 */
trait GetCollectionTrait
{
    /**
     * Get a paginated resource collection.
     *
     * @param array $filters collection search parameters
     * @param string $sortBy sort field
     * @param string $sortOrder sort order
     * @param int $itemsPerPage number of items per page
     *
     * @return ShipXCollection<static>
     */
    public static function getCollection(array $filters = [], $sortBy = '', $sortOrder = '', $itemsPerPage = 0)
    {
        return new ShipXCollection(static::class, $filters, $sortBy, $sortOrder, $itemsPerPage);
    }
}
