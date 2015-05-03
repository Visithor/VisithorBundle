<?php

/*
 * This file is part of the Visithor package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

namespace Visithor\Bundle\Environment\Interfaces;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Interface EnvironmentBuilderInterface
 */
interface EnvironmentBuilderInterface
{
    /**
     * Set up environment
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return $this Self object
     */
    public function setUp(KernelInterface $kernel);

    /**
     * Tear down environment
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return $this Self object
     */
    public function tearDown(KernelInterface $kernel);
}
