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
 * Class AbstractInjectionAware
 *
 * This abstract class offers common access to the DI in a class
 *
 * @package Phalcon\Di
 */
abstract class AbstractInjectionAware implements InjectionAwareInterface
{
    /**
     * Dependency Injector
     *
     * @var DiInterface $container
     */
    protected $container;

    /**
     * Returns the internal dependency injector
     *
     * @return DiInterface
     */
    public function getDI(): DiInterface
    {
        return $this->container;
    }

    /**
     * Sets the dependency injector
     *
     * @param DiInterface $container
     */
    public function setDI(DiInterface $container): void
    {
        $this->container = $container;
    }
}
