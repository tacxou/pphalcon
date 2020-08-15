<?php

declare(strict_types=1);

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

namespace Phalcon\Helper;

/**
 * Class Arr
 *
 * This class offers quick array functions throughout the framework
 *
 * @package Phalcon\Helper
 */
class Arr
{
    /**
     * Chunks an array into smaller arrays of a specified size.
     *
     * @param array $collection
     * @param int $size
     * @param bool $preserveKeys
     * @return array
     */
    final public static function chunk(array $collection, int $size, bool $preserveKeys = false): array
    {
        return array_chunk($collection, $size, $preserveKeys);
    }

    /**
     * Helper method to filter the collection
     *
     * @param array $collection
     * @param callable|null $method
     * @return array
     */
    final public static function filter(array $collection, $method = null): array
    {
        if (null === $method || !is_callable($method)) {
            return $collection;
        }

        return array_filter($collection, $method);
    }

    /**
     * Returns the first element of the collection.
     *
     * If a callable is passed, the element returned is the first that validates true
     *
     * @param array $collection
     * @param callable|null $method
     * @return mixed
     */
    final public static function first(array $collection, $method = null)
    {
        $filtered = self::filter($collection, $method);

        return reset($filtered);
    }

    /**
     * Returns the key of the first element of the collection.
     *
     * If a callable is passed, the element returned is the first that validates true
     *
     * @param array $collection
     * @param callable|null $method
     * @return mixed
     */
    final public static function firstKey(array $collection, $method = null)
    {
        $filtered = self::filter($collection, $method);

        reset($filtered);

        return key($filtered);
    }

    /**
     * Flattens an array up to the one level depth, unless `$deep` is set to `true`
     *
     * @param array $collection
     * @param bool $deep
     * @return array
     * @noinspection SlowArrayOperationsInLoopInspection
     */
    final public static function flatten(array $collection, bool $deep = false): array
    {
        $data = [];

        foreach ($collection as $item) {
            if (!is_array($item)) {
                $data[] = $item;
            } else {
                if ($deep) {
                    $data = array_merge(
                        $data,
                        self::flatten($item, true)
                    );
                } else {
                    $data = array_merge(
                        $data,
                        array_values($item)
                    );
                }
            }
        }

        return $data;
    }

    /**
     * Helper method to get an array element or a default
     *
     * @param array $collection
     * @param mixed $index
     * @param null $defaultValue
     * @param string|null $cast
     * @return mixed
     */
    final public static function get(array $collection, $index, $defaultValue = null, $cast = null)
    {
        $value = $collection[$index];

        if (!isset($value)) {
            return $defaultValue;
        }

        if ($cast) {
            settype($value, $cast);
        }

        return $value;
    }

    /**
     * Groups the elements of an array based on the passed callable
     *
     * @param array $collection
     * @param callable $method
     * @return array
     */
    final public static function group(array $collection, $method): array
    {
        $filtered = [];

        foreach ($collection as $element) {
            if ((!is_string($method) && is_callable($method)) || function_exists($method)) {
                $key = $method($element);
                $filtered[$key][] = $element;
            } elseif (is_object($element)) {
                $key = $element->$method();
                $filtered[$key][] = $element;
            } elseif (isset($element[$method])) {
                $key = $element[$method];
                $filtered[$key][] = $element;
            }
        }

        return $filtered;
    }

    /**
     * Helper method to get an array element or a default
     *
     * @param array $collection
     * @param mixed $index
     * @return bool
     */
    final public static function has(array $collection, $index): bool
    {
        return isset($collection[$index]);
    }

    /**
     * Checks a flat list for duplicate values.
     *
     * Returns true if duplicate values exist and false if values are all unique.
     *
     * @param array $collection
     * @return bool
     */
    final public static function isUnique(array $collection): bool
    {
        return count($collection) === count(array_unique($collection));
    }

    /**
     * Returns the last element of the collection.
     *
     * If a callable is passed, the element returned is the first that validates true
     *
     * @param array $collection
     * @param callable|null $method
     * @return mixed
     */
    final public static function last(array $collection, $method = null)
    {
        $filtered = self::filter($collection, $method);

        return end($filtered);
    }

    /**
     * Returns the key of the last element of the collection.
     *
     * If a callable is passed, the element returned is the first that validates true
     *
     * @param array $collection
     * @param callable|null $method
     * @return mixed
     */
    final public static function lastKey(array $collection, $method = null)
    {
        $filtered = self::filter($collection, $method);

        end($filtered);

        return key($filtered);
    }

    /**
     * Sorts a collection of arrays or objects by key
     *
     * @param array $collection
     * @param mixed $attribute
     * @param string $order
     * @return array
     */
    final public static function order(array $collection, $attribute, string $order = "asc"): array
    {
        $sorted = [];

        foreach ($collection as $item) {
            if (is_object($item)) {
                $key = $item->$attribute;
            } else {
                $key = $item[$attribute];
            }

            $sorted[$key] = $item;
        }

        if ($order === "asc") {
            ksort($sorted);
        } else {
            krsort($sorted);
        }

        return array_values($sorted);
    }

    /**
     * Retrieves all of the values for a given key:
     *
     * @param array $collection
     * @param string $element
     * @return array
     */
    final public static function pluck(array $collection, string $element): array
    {
        $filtered = [];

        foreach ($collection as $item) {
            if (is_object($item) && isset($item->$element)) {
                $filtered[] = $item->$element;
            } elseif (is_array($item) && isset($item[$element])) {
                $filtered[] = $item[$element];
            }
        }

        return $filtered;
    }

    /**
     * Helper method to set an array element
     *
     * @param array $collection
     * @param mixed $value
     * @param mixed $index
     * @return array
     */
    final public static function set(array $collection, $value, $index = null): array
    {
        if (null === $index) {
            $collection[] = $value;
        } else {
            $collection[$index] = $value;
        }

        return $collection;
    }

    /**
     * Returns a new array with n elements removed from the right.
     *
     * @param array $collection
     * @param int $elements
     * @return array
     */
    final public static function sliceLeft(array $collection, int $elements = 1): array
    {
        return array_slice($collection, 0, $elements);
    }

    /**
     * Returns a new array with the X elements from the right
     *
     * @param array $collection
     * @param int $elements
     * @return array
     */
    final public static function sliceRight(array $collection, int $elements = 1): array
    {
        return array_slice($collection, $elements);
    }

    /**
     * Returns a new array with keys of the passed array as one element and values as another
     *
     * @param array $collection
     * @return array
     */
    final public static function split(array $collection): array
    {
        return [
            array_keys($collection),
            array_values($collection)
        ];
    }

    /**
     * Returns the passed array as an object
     *
     * @param array $collection
     * @return object
     */
    final public static function toObject(array $collection)
    {
        return (object)$collection;
    }

    /**
     * Returns true if the provided function returns true for all elements of
     * the collection, false otherwise.
     *
     * @param array $collection
     * @param callable|null $method
     * @return bool
     */
    final public static function validateAll(array $collection, $method = null): bool
    {
        return count(self::filter($collection, $method)) === count($collection);
    }

    /**
     * Returns true if the provided function returns true for at least one
     * element of the collection, false otherwise.
     *
     * @param array $collection
     * @param callable|null $method
     * @return bool
     */
    final public static function validateAny(array $collection, $method = null): bool
    {
        return count(self::filter($collection, $method)) > 0;
    }

    /**
     * White list filter by key: obtain elements of an array filtering
     * by the keys obtained from the elements of a whitelist
     *
     * @param array $collection
     * @param array $whiteList
     * @return array
     */
    final public static function whiteList(array $collection, array $whiteList): array
    {
        /**
         * Clean whitelist, just strings and integers
         */
        $whiteList = array_filter(
            $whiteList,
            static function ($element) {
                return is_int($element) || is_string($element);
            }
        );

        return array_intersect_key(
            $collection,
            array_flip($whiteList)
        );
    }
}
