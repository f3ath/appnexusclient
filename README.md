#AppNexusClient
[![Total Downloads](https://img.shields.io/packagist/dt/f3ath/appnexus.svg)](https://packagist.org/packages/f3ath/appnexus)
[![Latest Stable Version](https://img.shields.io/packagist/v/f3ath/appnexus.svg)](https://packagist.org/packages/f3ath/appnexus)
[![Travis Build](https://travis-ci.org/f3ath/appnexus.svg?branch=master)](https://travis-ci.org/f3ath/appnexus)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/3637a8cf-8735-465a-b528-a4ad1edff017.svg)](https://insight.sensiolabs.com/projects/3637a8cf-8735-465a-b528-a4ad1edff017)

A simple Appnexus API client

#Install
Via [composer](https://getcomposer.org):
`$ composer require "f3ath/appnexus"`

#Use
```php
$storage = new F3\AppNexusClient\ArrayTokenStorage(); // Memcached and Apc storage are also available
$appnexus = new F3\AppNexusClient\AppNexusClient('username', 'password', "http://api-console.client-testing.adnxs.net/", $storage);
var_dump($appnexus->call(F3\AppNexusClient\HttpMethod::GET, '/user'));
```
