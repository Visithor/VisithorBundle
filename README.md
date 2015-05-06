Visithor Bundle for Symfony
===========================

[![Build Status](https://travis-ci.org/Visithor/VisithorBundle.png?branch=master)](https://travis-ci.org/Visithor/VisithorBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Visithor/VisithorBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Visithor/VisithorBundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/visithor/visithor-bundle/v/stable.png)](https://packagist.org/packages/visithor/visithor-bundle)
[![Latest Unstable Version](https://poser.pugx.org/visithor/visithor-bundle/v/unstable.png)](https://packagist.org/packages/visithor/visithor-bundle)

Symfony Bundle for PHP Package [visithor](http://github.com/visithor/visithor),
a library that provides you a simple and painless way of testing your 
application routes with specific HTTP Codes.

Please read [Visithor](http://github.com/visithor/visithor) documentation in 
order to understand the final purpose of this project and how you should create
your config files.

## Installation

All you need to do is add this package in your composer under `require-dev` 
block and you will be able to test your application.

``` yaml
'require-dev':
    ...
    
    'visithor/visithor-bundle': '~0.1'
```

Then you have to update your dependencies.

``` bash
php composer.phar update
```

## Integration

This Bundle integrates the project in your Symfony project. This means that adds
all the commands in your project console, so when you do `app/console` you will
see the `visithor:*` commands.

``` bash
php app/console visithor:go --env=test
```

## Config

This Bundle provides you some extra features when defining your urls. Now you
can define your routes using the route name and an array of parameters.

``` yml
defaults:
    #
    # This value can be a simple HTTP Code or an array of acceptable HTTP Codes
    # - 200
    # - [200, 301]
    #
    http_codes: [200, 302]

urls:
    #
    # By default, is there is no specified HTTP Code, then default one is used
    # as the valid one
    #
    - http://google.es
    - http://elcodi.io
    
    #
    # There are some other formats available as well
    #
    - [http://shopery.com, 200]
    - [http://shopery.com, [200, 302]]
    
    #
    # This Bundle adds some extra formats
    #
    - [store_homepage, 200]
    - [[store_category_products_list, {'slug': 'women-shirts', 'id': 1}], 200]
    - [[store_category_products_list, {'slug': 'another-name', 'id': 1}], 302]
    - [[store_homepage, {_locale: es}]]
```

## Environment

Maybe you need to prepare your environment before Visithor tests your routes,
right? Prepare your database, load your fixtures, and whatever you need to make
your test installation works.

By default, VisithorBundle has a simple implementation already working. This
implementation takes care about building the database and creating your schema.

``` bash
php app/console doctrine:database:create
php app/console doctrine:schema:update
```

It takes care as well of the destruction of your testing database once your test
is finished.

``` bash
php app/console doctrine:database:drop --force
```

If you want to extend this behavior, for example for some fixtures load, then
you need to do your own implementation, or extend this one.

To implement your own, you should define a service called 
`visitor.environment_builder` than implements the interface 
`Visithor\Bundle\Environment\Interfaces\EnvironmentBuilderInterface`.

If you take a look at this interface, you will se that you need to define two 
methods. The first one is intended to setUp your environment and will be called 
just once at the beginning of the suite. The second one will tear down such 
environment (for example removing database).

``` php
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
        //
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
        //
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
        //
    }
}
```

This is the way you can overwrite completely the default implementation, but if
you just want to extend it, then is much simpler. Take a look at this example.

``` php
namespace Elcodi\Common\VisithorBridgeBundle\Visithor;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Visithor\Bundle\Environment\SymfonyEnvironmentBuilder;

/**
 * Class EnvironmentBuilder
 */
class EnvironmentBuilder extends SymfonyEnvironmentBuilder
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
        parent::setUp($kernel);

        $this
            ->executeCommand('doctrine:fixtures:load', [
                '--fixtures' => $kernel
                        ->getRootDir() . '/../src/Elcodi/Fixtures',
            ])
            ->executeCommand('elcodi:templates:load')
            ->executeCommand('elcodi:templates:enable', [
                'template' => 'StoreTemplateBundle',
            ])
            ->executeCommand('elcodi:plugins:load')
            ->executeCommand('assets:install')
            ->executeCommand('assetic:dump');
    }
}
```

To call some commands you can use the protected method called `executeCommand`,
but remember to call the parent method in order to initialize the application 
and call the already existing code.

## Roles

You will, for sure, have the need to test your private routes. Of course, this
is a common need and this bundle satisfies it :)

Let's check our simple security file.

``` yaml
security:

    providers:
        in_memory:
            memory: ~

    firewalls:
        default:
            provider: in_memory
            http_basic: ~
            anonymous: ~

    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
        - { path: ^/superadmin, roles: ROLE_SUPERADMIN }
```

Then, let's see our Visithor configuration.

``` yaml
urls:
    - ['/', 200]
    - ['/admin', 200, {'role': 'ROLE_ADMIN', 'firewall': 'default'}]
    - ['/superadmin', 403, {'role': 'ROLE_ADMIN', 'firewall': 'default'}]
```

In this case, all routes are under the default firewall, called `default`.

Route `admin_route` is protected by the access role `ROLE_ADMIN`, and because we 
are testing against this role, then we'll receive a 200.

Route `superadmin_route` is protected by the access role `ROLE_SUPERADMIN`, but
in this case we are testing again using role `ROLE_ADMIN`, so we'll receive a
403 code.

Of course, you can define your firewall as a global option. Your routes will 
apply security only if both role and firewall options are defined.

``` yaml
defaults:
    options:
        firewall: default
urls:
    - ['/', 200]
    - ['/admin', 200, {'role': 'ROLE_ADMIN'}]
    - ['/superadmin', 403, {'role': 'ROLE_ADMIN'}]
```

Because you need to authenticate a real user in order to make it work, in your
own EnvironmentBuilder implementation you will be able to return this user. Make
sure that your testing environment is prepared to be tested.

Let's see a real example about an implementation of this method.

``` php
/**
 * Get authenticated user
 *
 * @param string $role Role
 *
 * @return mixed User for authentication
 */
public function getAuthenticationUser($role)
{
    return 'ROLE_ADMIN' === $role
        ? $this
            ->adminUserRepository
            ->findOneBy([
                'email' => 'admin@admin.com'
            ])
        : null;
}
```

As you can see, the parameter received is the role you are intended to test, so
you can switch between users depending on that value.
