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

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * AppKernel for testing
 */
class AppKernel extends Kernel
{
    /**
     * Registers all needed bundles
     */
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Visithor\Bundle\VisithorBundle(),
            new Visithor\Bundle\Tests\FakeBundle\FakeBundle(),
        ];
    }

    /**
     * Setup configuration file.
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(dirname(__FILE__) . '/config.yml');
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function getEnvironment()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     *
     * @api
     */
    public function isDebug()
    {
        return false;
    }
}
