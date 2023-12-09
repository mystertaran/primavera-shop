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

namespace InPost\Shipping\ShipX\Resource;

use ArrayAccess;
use Countable;
use InPost\Shipping\Api\Request;
use Iterator;

/**
 * @template T of ShipXResource
 * @template-implements \Iterator<int, T>
 */
class ShipXCollection implements Iterator, ArrayAccess, Countable
{
    /**
     * ShipXResource class name.
     *
     * @var class-string<T>
     */
    protected $type;

    /**
     * Resource collection.
     *
     * @var T[]
     */
    protected $items = [];

    /**
     * Collection iterator.
     *
     * @var int
     */
    protected $iterator = 0;

    /**
     * Number of items per page.
     *
     * @var int
     */
    protected $itemsPerPage;

    /**
     * Resource count.
     *
     * @var int
     */
    protected $count;

    /**
     * API Request object.
     *
     * @var Request
     */
    protected $request;

    /**
     * ShipXCollection constructor.
     *
     * @param class-string<T> $type ShipXResource class name
     * @param array $filters collection search params
     * @param string $sortBy sort field
     * @param string $sortOrder sort order
     * @param int $itemsPerPage number of items per page
     */
    public function __construct($type, array $filters = [], $sortBy = '', $sortOrder = '', $itemsPerPage = 0)
    {
        if (!class_exists($type) || !is_subclass_of($type, ShipXResource::class)) {
            throw new \InvalidArgumentException(sprintf('%s is not a %s', $type, ShipXResource::class));
        }

        $this->type = $type;

        unset($filters['page'], $filters['per_page'], $filters['sort_by'], $filters['sort_order']);
        $queryParams = $filters;

        $itemsPerPage = (int) $itemsPerPage;
        if ($itemsPerPage > 0) {
            $this->itemsPerPage = $itemsPerPage;
        }

        if (!empty($sortBy)) {
            $queryParams['sort_by'] = $sortBy;
        }

        $sortOrder = (string) \Tools::strtolower($sortOrder);

        if (in_array($sortOrder, ['asc', 'desc'])) {
            $queryParams['sort_order'] = $sortOrder;
        }

        $this->request = $type::cast([])
            ->getRequestFactory()
            ->createRequest('GET', $type::getBasePath())
            ->setQueryAggregator(self::commaAggregator())
            ->setQueryParams($queryParams);
    }

    /**
     * Return the current element.
     *
     * @see Iterator::current()
     *
     * @return T|null
     */
    public function current()
    {
        return isset($this[$this->iterator]) ? $this[$this->iterator] : null;
    }

    /**
     * Go to the next item.
     *
     * @see Iterator::next()
     */
    public function next()
    {
        ++$this->iterator;
    }

    /**
     * Get the current item index.
     *
     * @see Iterator::key()
     *
     * @return int
     */
    public function key()
    {
        return $this->iterator;
    }

    /**
     * Check if the current position is valid.
     *
     * @see Iterator::valid()
     *
     * @return bool
     */
    public function valid()
    {
        return $this->iterator < $this->count();
    }

    /**
     * Rewind the iterator to the first element.
     *
     * @see Iterator::rewind()
     */
    public function rewind()
    {
        $this->iterator = 0;
    }

    /**
     * Check if a collection item with a given offset exists.
     *
     * @see ArrayAccess::offsetExists()
     *
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $offset >= 0 && $offset < $this->count();
    }

    /**
     * Get a collection item with a given offset.
     *
     * @see ArrayAccess::offsetGet()
     *
     * @param int $offset
     *
     * @return T|null
     */
    public function offsetGet($offset)
    {
        if (isset($this[$offset]) && !isset($this->items[$offset])) {
            $this->getPage($this->calculatePageNumber($offset));
        }

        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * This operation is not supported.
     *
     * @see ArrayAccess::offsetSet()
     *
     * @param int $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * This operation is not supported.
     *
     * @see ArrayAccess::offsetUnset()
     *
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Get the number of items in the collection.
     *
     * @see Countable::count()
     *
     * @return int
     */
    public function count()
    {
        if (!isset($this->count)) {
            $this->getPage();
        }

        return $this->count;
    }

    /**
     * Get the page number for a given offset.
     *
     * @param int $offset
     *
     * @return int
     */
    protected function calculatePageNumber($offset)
    {
        if (!isset($this->itemsPerPage)) {
            $this->getPage();
        }

        return (int) ceil(++$offset / $this->itemsPerPage);
    }

    /**
     * Fetch a page from the API.
     *
     * @param int $pageNumber
     */
    protected function getPage($pageNumber = 1)
    {
        $queryParams = [
            'page' => (int) $pageNumber,
        ];

        if (isset($this->itemsPerPage)) {
            $queryParams['per_page'] = $this->itemsPerPage;
        }

        $page = $this->request
            ->setQueryParams($queryParams)
            ->send()
            ->json();

        if (!isset($this->itemsPerPage) || $page['per_page'] < $this->itemsPerPage) {
            $this->itemsPerPage = (int) $page['per_page'];
        }

        if (!isset($this->count)) {
            $this->count = (int) $page['count'];
        }

        $class = $this->type;
        $index = (int) (($pageNumber - 1) * $this->itemsPerPage);

        foreach ($class::castMany($page['items']) as $item) {
            $this->items[$index++] = $item;
        }

        /* ShipX sometimes returns fewer items than the declared count... */
        if (0 > $difference = $page['per_page'] - count($page['items'])) {
            $this->count -= $difference;
        }
    }

    protected static function commaAggregator()
    {
        return static function ($data) {
            return self::walkQuery($data, '', static function ($key, $prefix) {
                return is_int($key) ? $prefix : "{$prefix}[{$key}]";
            });
        };
    }

    protected static function walkQuery($data, $keyPrefix, callable $prefixer)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if ($keyPrefix) {
                $key = $prefixer($key, $keyPrefix);
            }
            if (is_array($value)) {
                $result += self::walkQuery($value, $key, $prefixer);
            } elseif (isset($result[$key])) {
                $result[$key][0] .= ',' . $value;
            } else {
                $result[$key] = [$value];
            }
        }

        return $result;
    }
}
