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

namespace InPost\Shipping\Repository;

use DateInterval;
use DateTimeImmutable;
use Db;
use DbQuery;
use InPost\Shipping\Entity\PointCache;
use InPost\Shipping\ShipX\Resource\Point;

class PointRepository
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    private $db;

    public function __construct(Db $db = null)
    {
        $this->db = isset($db) ? $db : Db::getInstance();
    }

    /**
     * @param string $pointId
     *
     * @return PointCache|null
     */
    public function findByPointId($pointId)
    {
        $query = (new DbQuery())
            ->from('inpost_point')
            ->where('id_point LIKE "' . pSQL($pointId) . '"');

        if (!$row = $this->db->getRow($query)) {
            return null;
        }

        return new PointCache(
            Point::fromJson($row['data']),
            DateTimeImmutable::createFromFormat(self::DATE_FORMAT, $row['date_upd']),
            new DateInterval('P7D')
        );
    }

    /** @return PointCache|null */
    public function insert(Point $point)
    {
        $cacheItem = new PointCache($this->filterPoint($point));
        $data = $this->formatData($cacheItem);
        $data['id_point'] = pSQL($point->getId());

        return $this->db->insert('inpost_point', $data)
            ? $cacheItem
            : null;
    }

    /** @return PointCache|null */
    public function update(Point $point)
    {
        $cacheItem = new PointCache($this->filterPoint($point));
        $data = $this->formatData($cacheItem);

        $result = $this->db->update(
            'inpost_point',
            $data,
            'id_point LIKE "' . pSQL($point->getId()) . '"'
        );

        return $result ? $cacheItem : null;
    }

    /** @return array */
    private function formatData(PointCache $cacheItem)
    {
        return [
            'data' => pSQL((string) $cacheItem->getPoint()),
            'date_upd' => $cacheItem->getUpdatedAt()->format(self::DATE_FORMAT),
        ];
    }

    private function filterPoint(Point $point)
    {
        return new Point([
            'name' => $point->name,
            'address' => $point->address,
            'address_details' => $point->address_details,
            'payment_available' => $point->payment_available,
            'location_247' => $point->location_247,
        ]);
    }
}
