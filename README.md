#AppNexusClient

A simple Appnexus API client

#Install
Via [composer](https://getcomposer.org):
`$ composer require "f3ath/appnexus"`

#Use
```php
$storage = new F3\AppNexusClient\ArrayTokenStorage(); // Memcached or Apc storage is also available
$appnexus = new F3\AppNexusClient\AppNexusClient('username', 'password', "http://api-console.client-testing.adnxs.net/", $storage);
var_dump($appnexus->call(F3\AppNexusClient\HttpMethod::GET, '/user'));
```
