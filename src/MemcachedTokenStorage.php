<?php
namespace F3\AppNexusClient;

use Memcached;

/**
 * MemcachedTokenStorage
 *
 * @uses TokenStorage
 * @package
 * @version $id$
 * @copyright Alexey Karapetov
 * @author Alexey Karapetov <karapetov@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 */
class MemcachedTokenStorage implements TokenStorage
{
    private $memcached;
    private $prefix;

    public function __construct(Memcached $memcached, $prefix = '')
    {
        $this->memcached = $memcached;
        $this->prefix = $prefix;
    }

    /**
     * set token
     *
     * @param string $username
     * @param string $token
     * @return void
     */
    public function set($username, $token)
    {
        $this->memcached->set($this->prefix.$username, $token);
    }

    /**
     * get token
     *
     * @param string $username
     * @return string|null
     */
    public function get($username)
    {
        return $this->memcached->get($this->prefix.$username);
    }
}
