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

use InvalidArgumentException;

/**
 * Class Json
 *
 * This class offers a wrapper for JSON methods to serialize and unserialize
 *
 * @package Phalcon\Helper
 */
class Json
{
    /**
     * Decodes a string using `json_decode` and throws an exception if the
     * JSON data cannot be decoded
     *
     * ```php
     * use Phalcon\Helper\Json;
     *
     * $data = '{"one":"two","0":"three"}';
     *
     * var_dump(Json::decode($data));
     * // [
     * //     'one' => 'two',
     * //     'three'
     * // ];
     * ```
     *
     * @param string $data
     * @param bool $associative
     * @param int $depth
     * @param int $options
     * @throws InvalidArgumentException if the JSON cannot be decoded.
     * @return mixed
     */
    final public static function decode(string $data, bool $associative = false, int $depth = 512, int $options = 0)
    {
        $decoded = json_decode($data, $associative, $depth, $options);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                "json_decode error: " . json_last_error_msg()
            );
        }

        return $decoded;
    }

    /**
     * Encodes a string using `json_encode` and throws an exception if the
     * JSON data cannot be encoded
     *
     * ```php
     * use Phalcon\Helper\Json;
     *
     * $data = [
     *     'one' => 'two',
     *     'three'
     * ];
     *
     * echo Json::encode($data);
     * // {"one":"two","0":"three"}
     * ```
     *
     * @link http://www.php.net/manual/en/function.json-encode.php
     * @param mixed $data
     * @param int $options
     * @param int $depth
     * @throws InvalidArgumentException if the JSON cannot be encoded.
     * @return string
     */
    final public static function encode($data, int $options = 0, int $depth = 512): string
    {
        $encoded = json_encode($data, $options, $depth);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(
                "json_encode error: " . json_last_error_msg()
            );
        }

        return $encoded;
    }
}
