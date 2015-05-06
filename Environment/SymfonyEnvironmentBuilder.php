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

namespace Visithor\Bundle\Environment;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;

use Visithor\Bundle\Environment\Interfaces\EnvironmentBuilderInterface;

/**
 * Class SymfonyEnvironmentBuilder
 */
class SymfonyEnvironmentBuilder implements EnvironmentBuilderInterface
{
    /**
     * @var Application
     *
     * Application
     */
    protected $application;

    /**
     * Set up environment
     *
     * @param KernelInterface $kernel Kernel
     *
     * @return $this Self object
     */
    public function setUp(KernelInterface $kernel)
    {
        $this->application = new Application($kernel);
        $this->application->setAutoExit(false);

        $this
            ->executeCommand('doctrine:database:drop', [
                '--force' => true,
            ])
            ->executeCommand('doctrine:database:create')
            ->executeCommand('doctrine:schema:create');
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
        $this
            ->executeCommand('doctrine:database:drop', [
                '--force' => true,
            ]);
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

    /**
     * Execute a command
     *
     * @param string $command    Command
     * @param array  $parameters Parameters
     *
     * @return $this Self object
     */
    protected function executeCommand(
        $command,
        array $parameters = []
    ) {
        $environment = $this
            ->application
            ->getKernel()
            ->getEnvironment();

        $this
            ->application
            ->run(new ArrayInput(array_merge(
                [
                    'command'          => $command,
                    '--no-interaction' => true,
                    '--env'            => $environment,
                    '--quiet'          => true,
                ], $parameters
            )));

        return $this;
    }
}
