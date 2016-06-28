<?php
namespace F3\AppNexusClient;

/**
 * ApcTokenStorage
 *
 * @uses TokenStorage
 * @copyright Federico Nicolás Motta
 * @author Federico Nicolás Motta <fedemotta@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php The MIT License (MIT)
 */
class ApcTokenStorage implements TokenStorage
{
    private $prefix;
    private $ttl;

    public function __construct($prefix = '',  $ttl = 0 )
    {
        $this->prefix = $prefix;
        $this->ttl = $ttl;
    }

    /**
     * set token
     *
     * @param string $username
     * @param string $token
     * @return bool
     */
    public function set($username, $token)
    {
        return apc_store($this->prefix.$username, $token, $this->ttl);
    }

    /**
     * get token
     *
     * @param string $username
     * @return string|false
     */
    public function get($username)
    {
        return apc_fetch($this->prefix.$username);
    }
}
