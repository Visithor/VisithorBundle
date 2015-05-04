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

namespace Visithor\Bundle\Generator;

use Symfony\Component\Routing\Exception\ExceptionInterface;
use Symfony\Component\Routing\RouterInterface;

use Visithor\Factory\UrlChainFactory;
use Visithor\Factory\UrlFactory;
use Visithor\Generator\UrlGenerator;

/**
 * Class SymfonyUrlGenerator
 */
class SymfonyUrlGenerator extends UrlGenerator
{
    /**
     * @var RouterInterface
     *
     * Router
     */
    protected $router;

    /**
     * Construct
     *
     * @param UrlFactory      $urlFactory      Url factory
     * @param UrlChainFactory $urlChainFactory UrlChain factory
     * @param RouterInterface $router          Router
     */
    public function __construct(
        UrlFactory $urlFactory,
        UrlChainFactory $urlChainFactory,
        RouterInterface $router
    ) {
        parent::__construct(
            $urlFactory,
            $urlChainFactory
        );

        $this->router = $router;
    }

    /**
     * Build the url given the configuration data
     *
     * @param mixed $urlConfig Url configuration
     *
     * @return string Route path
     */
    protected function getUrlPathFromConfig($urlConfig)
    {
        $urlPath = parent::getUrlPathFromConfig($urlConfig);

        try {
            $path = is_array($urlPath)
                ? $urlPath[0]
                : $urlPath;

            $arguments = (
                is_array($urlPath) &&
                isset($urlPath[1]) &&
                is_array($urlPath[1])
            )
                ? $urlPath[1]
                : [];

            $urlPath = $this
                ->router
                ->generate($path, $arguments);
        } catch (ExceptionInterface $e) {

            /**
             * Silent pass
             */
        }

        return $urlPath;
    }
}
