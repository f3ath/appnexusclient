<?php
namespace F3\AppNexusClient;

class ServerException extends \Exception
{
	private $response;

	public function __construct($response)
	{
		parent::__construct(sprintf('%s, %s', $response->error, $response->error_description));
		$this->response = $response;
	}

	/**
	 * getResponse
	 *
	 * @return object
	 */
	public function getResponse()
	{
		return $this->response;
	}
}
