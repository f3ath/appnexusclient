<?php
namespace F3\AppNexusClient;

class AppNexusClient
{
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';

	private $curl;
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
	 * @param Curl $curl
	 * @param TokenStorage $tokenStorage Auth token storage
	 */
	public function __construct($username, $password, $host, Curl $curl, TokenStorage $tokenStorage)
	{
		$this->username = $username;
		$this->password = $password;
		$this->host = $host;
		$this->curl = $curl;
		$this->tokenStorage = $tokenStorage;
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
		return $this->http(self::POST, '/auth', $post)->token;
	}

	/**
	 * Do raw HTTP call
	 *
	 * @param string $method
	 * @param string $path
	 * @param array $post POST data
	 * @param array $headers
	 *
	 * @return object response
	 */
	protected function http($method, $path, array $post = array(), array $headers = array())
	{
		$options = array(
			CURLOPT_URL => $this->host.$path,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => $headers,
		);
		switch ($method) {
			case self::GET:
				$options[CURLOPT_POST] = false;
				break;
			case self::POST:
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = json_encode($post);
				break;
			case self::PUT:
				$options[CURLOPT_CUSTOMREQUEST] = 'PUT';
				$options[CURLOPT_POSTFIELDS] = json_encode($post);
				break;
			case self::DELETE:
				$options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
				$options[CURLOPT_POSTFIELDS] = json_encode($post);
				break;
			default:
				throw new \InvalidArgumentException(sprintf('Invalid method: %s', $method));
		}

		$this->curl->init();
		$this->curl->setOptArray($options);
		$json = $this->curl->exec();
		$response = json_decode($json);
		if (!isset($response->response)) {
			throw new \RuntimeException(sprintf('Unexpected response: %s', $json));
		}
		$response = $response->response;
		if (isset($response->status) && 'OK' == $response->status) {
			return $response;
		} elseif ('NOAUTH' == $response->error_id) {
			throw new TokenExpiredException();
		}
		throw new ServerException($response);
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
				return $this->http($method, $path, $post, array(sprintf('Authorization: %s', $token)));
			} catch (TokenExpiredException $tokenExpired) {
				$token = null; // drop the cached token
			}
		} while ($useCachedToken); // retry if cached token has been used
		throw $tokenExpired; // this means we have a fresh token expired
	}
}
