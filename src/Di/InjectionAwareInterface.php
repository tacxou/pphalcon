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

namespace Phalcon\Di;

/**
 * Interface InjectionAwareInterface
 *
 * @package Phalcon\Di
 */
interface InjectionAwareInterface
{
    /**
     * Sets the dependency injector
     *
     * @param DiInterface $container
     */
    public function setDI(DiInterface $container): void;

    /**
     * Returns the internal dependency injector
     *
     * @return DiInterface
     */
    public function getDI(): DiInterface;
}
