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

namespace Visithor\Bundle\Client;

use Exception;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\Router;

use Visithor\Bundle\Environment\Interfaces\EnvironmentBuilderInterface;
use Visithor\Client\Interfaces\ClientInterface;
use Visithor\Model\Url;

/**
 * Class SymfonyClient
 */
class SymfonyClient implements ClientInterface
{
    /**
     * @var ClientInterface
     *
     * Client
     */
    protected $client;

    /**
     * @var KernelInterface
     *
     * Kernel
     */
    protected $kernel;

    /**
     * @var EnvironmentBuilderInterface
     *
     * Environment Builder
     */
    protected $environmentBuilder;

    /**
     * Construct
     *
     * @param EnvironmentBuilderInterface $environmentBuilder Environment Builder
     */
    public function __construct(
        EnvironmentBuilderInterface $environmentBuilder = null
    )
    {
        $this->kernel = new \AppKernel('test', false);
        $this->kernel->boot();

        $this->client = $this
            ->kernel
            ->getContainer()
            ->get('test.client');

        if ($environmentBuilder instanceof EnvironmentBuilderInterface) {

            $this->environmentBuilder = $environmentBuilder;
            $environmentBuilder->setUp($this->kernel);
        }
    }

    /**
     * Get the HTTP Code Response given an URL instance
     *
     * @param Url $url Url
     *
     * @return int Response HTTP Code
     */
    public function getResponseHTTPCode(Url $url)
    {
        try {
            $this
                ->client
                ->request('GET', $url->getPath());

            $result = $this
                ->client
                ->getResponse()
                ->getStatusCode();

        } catch (Exception $e) {

            $result = 500;
        }

        return $result;
    }

    /**
     * Destroy
     */
    public function __destruct()
    {
        if ($this->environmentBuilder instanceof EnvironmentBuilderInterface) {

            $this->environmentBuilder->setUp($this->kernel);
        }
    }
}
