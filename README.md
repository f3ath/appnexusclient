#AppNexusClient

A simple Appnexus API client

#Install

Via composer:
`$ composer require "f3ath/appnexus"`

#Use

First, implement a token storage. As an example, here is a storage making use of arrays


```php
class ArrayTokenStorage implements F3\AppNexusClient\TokenStorage
{
    private $storage = array();

    public function set($username, $token)
    {
        $this->storage[$username] = $token;
    }

    public function get($username)
    {
        return isset($this->storage[$username]) ? $this->storage[$username] : null;
    }
}
```

This is enough to make simple calls


```php
$appnexus = new F3\AppNexusClient\AppNexusClient('username', 'password', "http://sand.api.appnexus.com", new ArrayTokenStorage());
var_dump($appnexus->call(F3\AppNexusClient\HttpMethod::GET, '/user'));
```
