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

namespace Visithor\Bundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\Client;

/**
 * Class VisithorGoTest
 */
class VisithorGoTest extends WebTestCase
{
    /**
     * @var Client
     *
     * client
     */
    protected $client;

    /**
     * @var Application
     *
     * application
     */
    protected static $application;

    /**
     * Setup
     */
    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        static::$application = new Application(static::$kernel);
        static::$application->setAutoExit(false);
        $this->client = static::createClient();
    }

    /**
     * Test visithor:go
     */
    public function testVisithorGo()
    {
        $result = static::$application->run(new ArrayInput([
            'command' => 'visithor:go',
            '--config' => static::$kernel->getRootDir(),
            '--quiet' => true,
        ]));

        $this->assertEquals(
            0,
            $result
        );
    }
}
