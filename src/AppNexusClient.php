<?php
namespace F3\AppNexusClient;

use InvalidArgumentException;
use RuntimeException;

class AppNexusClient
{
    private $http;
    private $host;
    private $username;
    private $password;
    private $tokenStorage;

    /**
     * __construct
     *
     * @param string $username
     * @param string $password
     * @param string $host AppNexus hostname including "http://", example: http://api.adnxs.com
     * @param TokenStorage $tokenStorage Auth token storage
     * @param HttpClient $http
     */
    public function __construct($username, $password, $host, TokenStorage $tokenStorage, HttpClient $http = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->host = $host;
        $this->tokenStorage = $tokenStorage;
        $this->http = $http ?: new HttpClient();
    }

    /**
     * Get a new auth token from server
     *
     * @return string
     */
    public function getNewToken()
    {
        $post = array(
            'auth' => array(
                'username' => $this->username,
                'password' => $this->password,
            ),
        );
        return $this->http->call(HttpMethod::POST, $this->host.'/auth', $post)->token;
    }

    /**
     * Call the server, (re)authenticating if necessary
     *
     * @param string $method (GET|POST|PUT|DELETE)
     * @param string $path
     * @param array $post POST data
     * @return object Response object
     */
    public function call($method, $path, array $post = array())
    {
        $useCachedToken = true;
        $token = $this->tokenStorage->get($this->username);
        do {
            if (!$token) { // expired or no token
                $token = $this->getNewToken();
                $this->tokenStorage->set($this->username, $token);
                $useCachedToken = false;
            }
            try {
                return $this->http->call($method, $this->host.$path, $post, array(sprintf('Authorization: %s', $token)));
            } catch (TokenExpiredException $tokenExpired) {
                $token = null; // drop the cached token
            }
        } while ($useCachedToken); // retry if a cached token has been used
        throw $tokenExpired; // this means we have a fresh token just expired
    }
}
