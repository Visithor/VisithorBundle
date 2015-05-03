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

namespace Visithor\Bundle\Command;

use Symfony\Component\Console\Output\OutputInterface;

use Visithor\Command\GoCommand as OriginalGoCommand;
use Visithor\Executor\Executor;
use Visithor\Generator\UrlGenerator;
use Visithor\Renderer\RendererFactory;

/**
 * Class GoCommand
 */
class GoCommand extends OriginalGoCommand
{
    /**
     * @var UrlGenerator
     *
     * URL instances generator
     */
    protected $urlGenerator;

    /**
     * @var RendererFactory
     *
     * Renderer factory
     */
    protected $rendererFactory;

    /**
     * @var Executor
     *
     * Visithor Executor
     */
    protected $executor;

    /**
     * Construct
     *
     * @param UrlGenerator    $urlGenerator    Url generator
     * @param RendererFactory $rendererFactory Render factory
     * @param Executor        $executor        Executor
     */
    function __construct(
        UrlGenerator $urlGenerator,
        RendererFactory $rendererFactory,
        Executor $executor
    )
    {
        parent::__construct();

        $this->urlGenerator = $urlGenerator;
        $this->rendererFactory = $rendererFactory;
        $this->executor = $executor;
    }


    /**
     * Executes all business logic inside this command
     *
     * This method returns 0 if all executions passed. 1 otherwise.
     *
     * @param OutputInterface $output Output
     * @param array           $config Config
     * @param string          $format Format
     *
     * @return integer Execution return
     */
    protected function executeVisithor(
        OutputInterface $output,
        array $config,
        $format
    )
    {
        $urlChain = $this
            ->urlGenerator
            ->generate($config);

        $renderer = $this
            ->rendererFactory
            ->create($format);

        return $this
            ->executor
            ->execute(
                $urlChain,
                $renderer,
                $output
            );
    }
}
