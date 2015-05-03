Visithor Bundle for Symfony
===========================

[![Build Status](https://travis-ci.org/visithor/VisithorBundle.png?branch=master)](https://travis-ci.org/visithor/VisithorBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Visithor/VisithorBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Visithor/VisithorBundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/visithor/visithor-bundle/v/stable.png)](https://packagist.org/packages/visithor/visithor-bundle)
[![Latest Unstable Version](https://poser.pugx.org/visithor/visithor-bundle/v/unstable.png)](https://packagist.org/packages/visithor/visithor-bundle)

Symfony Bundle for PHP Package [visithor](http://github.com/visithor/visithor),
a library that provides you a simple and painless way of testing your 
application routes with specific HTTP Codes.

Please read [Visithor](http://github.com/visithor/visithor) documentation in 
order to understand the final purpose of this project and how you should create
your config files.

## Integration

This Bundle integrates the project in your Symfony project. This means that adds
all the commands in your project console, so when you do `app/console` you will
see the `visithor:*` commands.

``` bash
php app/console visithor:go
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
