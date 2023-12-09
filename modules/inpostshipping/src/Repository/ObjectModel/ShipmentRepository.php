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

namespace InPost\Shipping\Repository\ObjectModel;

use InPost\Shipping\ShipX\Resource\Status;

class ShipmentRepository
{
    private $db;

    public function __construct(\Db $db = null)
    {
        $this->db = $db ?: \Db::getInstance();
    }

    /**
     * @param bool $sandbox
     * @param int $organizationId
     * @param int[] $shipmentIds
     *
     * @return \Generator<int, \InPostShipmentModel>
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getNotFinalizedShipments($sandbox, $organizationId, array $shipmentIds = [])
    {
        $qb = (new \DbQuery())
            ->from('inpost_shipment', 'i')
            ->where('i.sandbox = ' . $sandbox ? 1 : 0)
            ->where('i.organization_id = ' . (int) $organizationId)
            ->where('i.status NOT IN ("' . implode('","', Status::FINAL_STATUSES) . '")')
            ->orderBy('i.id_shipment');

        if ([] !== $shipmentIds) {
            $qb->where('i.id_shipment IN (' . implode(array_map('intval', $shipmentIds)) . ')');
        }

        return $this->getIterator($qb, 100);
    }

    /**
     * @param int $limit
     *
     * @return \Generator<int, \InPostShipmentModel>
     */
    private function getIterator(\DbQuery $qb, $limit = 0)
    {
        $offset = 0;
        $limit = (int) $limit;

        do {
            $qb->limit($limit, $offset);

            if (false === $result = $this->db->query($qb)) {
                throw new \PrestaShopDatabaseException($this->db->getMsgError());
            }

            while ($row = $this->db->nextRow($result)) {
                yield $row['id_shipment'] => $this->hydrate($row);
            }

            $offset += $limit;
        } while ($offset && $this->db->numRows());
    }

    /**
     * @return \InPostShipmentModel|null
     */
    private function hydrate(array $data)
    {
        if ([] === $data) {
            return null;
        }

        $shipment = new \InPostShipmentModel();
        $shipment->hydrate($data);

        return $shipment;
    }
}
