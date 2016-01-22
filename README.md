#appnexusclientClient
[![Total Downloads](https://img.shields.io/packagist/dt/f3ath/appnexusclient.svg)](https://packagist.org/packages/f3ath/appnexusclient)
[![Latest Stable Version](https://img.shields.io/packagist/v/f3ath/appnexusclient.svg)](https://packagist.org/packages/f3ath/appnexusclient)
[![Travis Build](https://travis-ci.org/f3ath/appnexusclient.svg?branch=master)](https://travis-ci.org/f3ath/appnexusclient)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/3637a8cf-8735-465a-b528-a4ad1edff017.svg)](https://insight.sensiolabs.com/projects/3637a8cf-8735-465a-b528-a4ad1edff017)

A simple appnexusclient API client

#Install
Via [composer](https://getcomposer.org):
`$ composer require "f3ath/appnexusclient"`

#Use
```php
$storage = new F3\appnexusclientClient\ArrayTokenStorage(); // Memcached and Apc storage are also available
$appnexusclient = new F3\appnexusclientClient\appnexusclientClient('username', 'password', "http://api-console.client-testing.adnxs.net/", $storage);
var_dump($appnexusclient->call(F3\appnexusclientClient\HttpMethod::GET, '/user'));
```
