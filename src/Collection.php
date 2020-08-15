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

namespace Phalcon;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Phalcon\Helper\Json;
use Serializable;
use Traversable;

class Collection implements
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    Serializable
{
    protected $data = [];

    protected $insensitive = true;

    protected $lowerKeys = [];

    /**
     * Collection constructor.
     *
     * @param array $data
     * @param bool $insensitive
     */
    public function __construct(array $data = [], bool $insensitive = true)
    {
        $this->insensitive = $insensitive;
        $this->init($data);
    }

    /**
     * Magic getter to get an element from the collection
     *
     * @param string $element
     * @return mixed
     */
    public function __get(string $element)
    {
        return $this->get($element);
    }

    /**
     * Magic isset to check whether an element exists or not
     *
     * @param string $element
     * @return bool
     */
    public function __isset(string $element): bool
    {
        return $this->has($element);
    }

    /**
     * Magic setter to assign values to an element
     *
     * @param string $element
     * @param $value
     */
    public function __set(string $element, $value): void
    {
        $this->set($element, $value);
    }

    /**
     * Magic unset to remove an element from the collection
     *
     * @param string $element
     */
    public function __unset(string $element): void
    {
        $this->remove($element);
    }

    /**
     * Clears the internal collection
     */
    public function clear(): void
    {
        $this->data = [];
        $this->lowerKeys = [];
    }

    /**
     * Count elements of an object.
     *
     * @see https://php.net/manual/en/countable.count.php
     * @return int
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Get the element from the collection
     *
     * @param string $element
     * @param null $defaultValue
     * @param string|null $cast
     * @return mixed|null
     */
    public function get(string $element, $defaultValue = null, string $cast = null)
    {
        if ($this->insensitive) {
            $element = strtolower($element);
        }

        $key = $this->lowerKeys[$element];

        if (!isset($key)) {
            return $defaultValue;
        }

        $value = $this->data[$key];

        if (null !== $cast) {
            settype($value, $cast);
        }

        return $value;
    }

    /**
     * Returns the iterator of the class
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Return collection keys
     *
     * @param bool $insensitive
     * @return array
     */
    public function getKeys(bool $insensitive = true): array
    {
        if ($insensitive) {
            return array_keys($this->lowerKeys);
        }

        return array_keys($this->data);
    }

    /**
     * Return collection values
     *
     * @return array
     */
    public function getValues(): array
    {
        return array_values($this->data);
    }

    /**
     * Get the element from the collection
     *
     * @param string $element
     * @return bool
     */
    public function has(string $element): bool
    {
        if ($this->insensitive) {
            $element = strtolower($element);
        }

        return isset($this->lowerKeys[$element]);
    }

    /**
     * Initialize internal array
     *
     * @param array $data
     */
    public function init(array $data = []): void
    {
        foreach ($data as $key => $value) {
            $this->setData($key, $value);
        }
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @see https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return array
     */
    public function jsonSerialize(): array
    {
        $records = [];

        foreach ($this->data as $key => $value) {
            if (is_object($value) && method_exists($value, "jsonSerialize")) {
                $records[$key] = $value->jsonSerialize();
            } else {
                $records[$key] = $value;
            }
        }

        return $records;
    }

    /**
     * Whether a offset exists
     *
     * @see https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $element
     * @return bool
     */
    public function offsetExists($element): bool
    {
        $element = (string)$element;

        return $this->has($element);
    }

    /**
     * Offset to retrieve
     *
     * @see https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $element
     * @return mixed
     */
    public function offsetGet($element)
    {
        $element = (string)$element;

        return $this->get($element);
    }

    /**
     * Offset to set
     *
     * @see https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $element
     * @param mixed $value
     * @return mixed
     */
    public function offsetSet($element, $value): void
    {
        $element = (string)$element;

        $this->set($element, $value);
    }

    /**
     * Offset to unset
     *
     * @see https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $element
     * @return mixed
     */
    public function offsetUnset($element): void
    {
        $element = (string)$element;

        $this->remove($element);
    }

    /**
     * Delete the element from the collection
     *
     * @param string $element
     */
    public function remove(string $element): void
    {
        if ($this->has($element)) {
            if ($this->insensitive) {
                $element = strtolower($element);
            }

            $data = $this->data;
            $lowerKeys = $this->lowerKeys;
            $key = $lowerKeys[$element];

            unset($lowerKeys[$element], $data[$key]);

            $this->data = $data;
            $this->lowerKeys = $lowerKeys;
        }
    }

    /**
     * Set an element in the collection
     *
     * @param string $element
     * @param mixed $value
     */
    public function set(string $element, $value): void
    {
        $this->setData($element, $value);
    }

    /**
     * String representation of object
     *
     * @see https://php.net/manual/en/serializable.serialize.php
     * @return string
     */
    public function serialize(): string
    {
        return serialize($this->toArray());
    }

    /**
     * Returns the object in an array format
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->data;
    }

    /**
     * Returns the object in a JSON format
     *
     * The default string uses the following options for json_encode
     *
     * `JSON_HEX_TAG`, `JSON_HEX_APOS`, `JSON_HEX_AMP`, `JSON_HEX_QUOT`,
     * `JSON_UNESCAPED_SLASHES`
     *
     * @see https://www.ietf.org/rfc/rfc4627.txt
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 79): string
    {
        return Json::encode($this->toArray(), $options);
    }

    /**
     * Constructs the object
     *
     * @see https://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized
     * @todo check unserialize options
     */
    public function unserialize($serialized): void
    {
        $serialized = (string)$serialized;
        $data = unserialize($serialized, ['allowed_classes' => false]);

        $this->init($data);
    }

    /**
     * @param string $element
     * @param $value
     */
    protected function setData(string $element, $value): void
    {
        $key = $this->insensitive ? strtolower($element) : $element;

        $this->data[$element] = $value;
        $this->lowerKeys[$key] = $element;
    }
}
