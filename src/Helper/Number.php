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
 * Class Number
 *
 * This class offers numeric functions for the framework
 *
 * @package Phalcon\Helper
 */
class Number
{
    /**
     * Helper method to get an array element or a default
     *
     * @param int $value
     * @param int $from
     * @param int $to
     * @return bool
     */
    final public static function between(int $value, int $from, int $to): bool
    {
        return $value >= $from && $value <= $to;
    }
}
