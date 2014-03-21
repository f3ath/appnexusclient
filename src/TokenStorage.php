<?php
namespace F3\AppNexusClient;

interface TokenStorage
{
	/**
	 * set
	 *
	 * @param string $username
	 * @param string $token
	 * @return void
	 */
	public function set($username, $token);

	/**
	 * get token for given username
	 *
	 * @param string $username
	 * @return string
	 */
	public function get($username);
}
