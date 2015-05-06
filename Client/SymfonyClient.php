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
use Symfony\Bundle\FrameworkBundle\Client as FrameworkClient;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Visithor\Bundle\Environment\Interfaces\EnvironmentBuilderInterface;
use Visithor\Client\Interfaces\ClientInterface;
use Visithor\Model\Url;

/**
 * Class SymfonyClient
 */
class SymfonyClient implements ClientInterface
{
    /**
     * @var FrameworkClient
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
     * @var SessionInterface
     *
     * Session
     */
    protected $session;

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
        SessionInterface $session,
        EnvironmentBuilderInterface $environmentBuilder = null
    ) {
        $this->session = $session;
        $this->environmentBuilder = $environmentBuilder;
    }

    /**
     * Build client
     *
     * @return $this Self object
     */
    public function buildClient()
    {
        $this->kernel = new \AppKernel('test', false);
        $this->kernel->boot();
        $this->session->clear();

        $this->client = $this
            ->kernel
            ->getContainer()
            ->get('test.client');

        $this
            ->environmentBuilder
            ->setUp($this->kernel);
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
            $this->authenticate($url);
            $verb = $url->getOption('verb', 'GET');

            $this
                ->client
                ->request($verb, $url->getPath());

            $result = $this
                ->client
                ->getResponse()
                ->getStatusCode();
        } catch (AccessDeniedHttpException $e) {
            $result = $e->getStatusCode();
        } catch (Exception $e) {
            $result = 'ERR';
        }

        $this->expireAuthentication($url);

        return $result;
    }

    /**
     * Authenticates a user if is needed.
     *
     * A user is needed to be authenticated if in the url a role and a firewall
     * is specified. Otherwise, the system will understand that is a public url
     *
     * @param Url $url Url
     *
     * @return $this Self object
     */
    protected function authenticate(Url $url)
    {
        if (
            !$url->getOption('role') ||
            !$url->getOption('firewall')
        ) {
            return $this;
        }

        $session = $this->session;
        $firewall = $url->getOption('firewall');
        $role = $url->getOption('role');
        $user = $this
            ->environmentBuilder
            ->getAuthenticationUser($url->getOption('role'));

        $token = new UsernamePasswordToken($user, null, $firewall, [$role]);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie(
            $session->getName(),
            $session->getId()
        );
        $this
            ->client
            ->getCookieJar()
            ->set($cookie);

        return $this;
    }

    /**
     * Expires the authentication if these has been created
     *
     * @param Url $url Url
     *
     * @return $this Self object
     */
    protected function expireAuthentication(Url $url)
    {
        $session = $this->session;
        $session->remove('_security_' . $url->getOption('firewall'));
        $session->save();

        $this
            ->client
            ->getCookieJar()
            ->expire($session->getName());

        return $this;
    }

    /**
     * Destroy client
     *
     * @return $this Self object
     */
    public function destroyClient()
    {
        $this
            ->environmentBuilder
            ->tearDown($this->kernel);
    }
}
