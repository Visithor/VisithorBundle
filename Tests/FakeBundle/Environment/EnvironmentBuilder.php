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

namespace Visithor\Bundle\Tests\FakeBundle\Environment;

use Symfony\Component\HttpKernel\KernelInterface;

use Visithor\Bundle\Environment\Interfaces\EnvironmentBuilderInterface;

/**
 * Class EnvironmentBuilder
 */
class EnvironmentBuilder implements EnvironmentBuilderInterface
{
    /**
     * Set up environment
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return $this Self object
     */
    public function setUp(KernelInterface $kernel)
    {
        return $this;
    }

    /**
     * Tear down environment
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return $this Self object
     */
    public function tearDown(KernelInterface $kernel)
    {
        return $this;
    }

    /**
     * Get authenticated user
     *
     * @param string $role Role
     *
     * @return mixed User for authentication
     */
    public function getAuthenticationUser($role)
    {
        return 'admin';
    }
}
