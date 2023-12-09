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

namespace InPost\Shipping\Entity;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use InPost\Shipping\ShipX\Resource\Point;

class PointCache
{
    private $point;
    private $updatedAt;
    private $ttl;

    public function __construct(
        Point $point,
        DateTimeInterface $updatedAt = null,
        DateInterval $ttl = null
    ) {
        $this->point = $point;
        $this->updatedAt = isset($updatedAt) ? $updatedAt : new DateTimeImmutable();
        $this->ttl = $ttl;
    }

    /** @return Point */
    public function getPoint()
    {
        return $this->point;
    }

    /** @return DateTimeInterface */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /** @return self */
    public function setUpdatedAt(DateTimeInterface $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /** @return bool */
    public function isFresh()
    {
        if (!isset($this->ttl)) {
            return true;
        }

        return new DateTimeImmutable() < $this->updatedAt->add($this->ttl);
    }

    /** @return self */
    public function setTtl(DateInterval $ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }
}
