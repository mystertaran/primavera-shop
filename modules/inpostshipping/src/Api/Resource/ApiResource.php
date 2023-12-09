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

namespace InPost\Shipping\Api\Resource;

use ArrayAccess;
use Carbon\Carbon;
use InPost\Shipping\Api\RequestFactoryInterface;
use JsonSerializable;
use stdClass;

abstract class ApiResource implements ArrayAccess, JsonSerializable
{
    /**
     * Format accepted by date() used to cast datetime fields.
     *
     * @const string
     */
    const DATE_FORMAT = 'Y-m-d\TH:i:s.uP';

    /**
     * Resource attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Attribute cast types.
     *
     * @var string[]
     */
    protected static $casts = [];

    /**
     * @param array $data attributes
     */
    public function __construct(array $data = [])
    {
        $this->mergeAttributes($data);
    }

    /**
     * Dynamically retrieve attributes on the resource.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the resource.
     *
     * @param string $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Determine if the specified key is an attribute for the resource.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Retrieve an attribute on the resource.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getAttribute($key)
    {
        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    /**
     * Set an attribute on the resource.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (null !== $value && $this->castsAttribute($key)) {
            $value = $this->castAs($value, $this->getAttributeCastType($key));
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    /**
     * Overwrite attribute values.
     *
     * @param array $attributes values to set
     * @param bool $clear remove all previous data
     *
     * @return $this
     */
    public function setAttributes(array $attributes, $clear = false)
    {
        if ($clear) {
            $this->attributes = [];
        }

        return $this->mergeAttributes($attributes);
    }

    /**
     * Merge attribute values into the attributes array.
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function mergeAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Determine if the specified attribute should be cast.
     *
     * @param string $key
     *
     * @return bool
     */
    public function castsAttribute($key)
    {
        return array_key_exists($key, static::$casts);
    }

    /**
     * Get the cast type for the specified attribute.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function getAttributeCastType($key)
    {
        return isset(static::$casts[$key]) ? static::$casts[$key] : null;
    }

    /**
     * Cast value as Carbon object (no time component).
     *
     * @param string|int $value
     *
     * @return Carbon
     */
    protected function castAsDate($value)
    {
        return $this->castAsDateTime($value)->startOfDay();
    }

    /**
     * Cast value as Carbon object.
     *
     * @param string|int $value
     *
     * @return Carbon
     */
    protected function castAsDateTime($value)
    {
        if (is_int($value)) {
            return Carbon::createFromTimestamp($value);
        } else {
            return Carbon::createFromFormat(static::DATE_FORMAT, $value);
        }
    }

    /**
     * Cast value to an ApiResource object instance.
     *
     * @param mixed $value
     * @param string $type
     *
     * @return ApiResource[]|ApiResource|null
     */
    protected function castAsClass($value, $type)
    {
        if (class_exists($type) && is_subclass_of($type, ApiResource::class)) {
            $collection = true;

            foreach ($value as $key => $item) {
                if (!is_int($key) || !is_array($item)) {
                    $collection = false;
                    break;
                }
            }

            if ($collection) {
                return $type::castMany($value);
            } else {
                return $type::cast($value);
            }
        }

        return null;
    }

    /**
     * Get ApiResource casted attribute value.
     *
     * @param mixed $value
     * @param string $type
     *
     * @return mixed
     */
    protected function castAs($value, $type)
    {
        switch ($type) {
            case 'bool':
                return (bool) $value;
            case 'date':
                return $this->castAsDate($value);
            case 'datetime':
                return $this->castAsDateTime($value);
            case 'float':
                return (float) $value;
            case 'int':
                return (int) $value;
            case 'string':
                return (string) $value;
            default:
                return $this->castAsClass($value, $type);
        }
    }

    /**
     * Convert ApiResource instance to JSON string.
     *
     * @return string
     */
    public function toJson()
    {
        return json_encode($this);
    }

    /**
     * Create a new ApiResource instance from JSON.
     *
     * @param string $data
     *
     * @return static
     */
    public static function fromJson($data)
    {
        return new static(json_decode($data, true));
    }

    /**
     * Get id field key.
     *
     * @return string
     */
    public static function getIdField()
    {
        return 'id';
    }

    /**
     * Get id field value.
     *
     * @return string|int
     */
    public function getId()
    {
        return $this->getAttribute(static::getIdField());
    }

    /**
     * Create new ApiResource instance from raw data.
     *
     * @param array|stdClass $data
     *
     * @return static
     */
    public static function cast($data)
    {
        return new static((array) $data);
    }

    /**
     * Create new ApiResource instances from raw data.
     *
     * @param array[]|stdClass[] $data
     *
     * @return static[]
     */
    public static function castMany(array $data)
    {
        return array_map('static::cast', $data);
    }

    /**
     * Return ApiResource raw data.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];

        foreach ($this->attributes as $key => $value) {
            $array[$key] = $this->serializeValue($value);
        }

        return $array;
    }

    protected function serializeValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->serializeValue($item);
            }
        } elseif ($value instanceof Carbon) {
            $value = $value->format(static::DATE_FORMAT);
        } elseif (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        }

        return $value;
    }

    /**
     * Get the request factory.
     *
     * @return RequestFactoryInterface
     */
    abstract public function getRequestFactory();

    /**
     * Get class API URI path.
     *
     * @return string
     */
    public static function getBasePath()
    {
        return static::BASE_PATH;
    }

    /**
     * Get resource API URI path.
     *
     * @return string
     */
    public static function getResourcePath()
    {
        return rtrim(static::BASE_PATH, '/') . '/{' . static::getIdField() . '}';
    }

    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this[$offset]) ? $this->attributes[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function __toString()
    {
        return json_encode($this);
    }
}
