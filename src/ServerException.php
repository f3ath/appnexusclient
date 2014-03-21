<?php
namespace F3\AppNexusClient;

use RuntimeException;
use stdClass;

class ServerException extends RuntimeException
{
	private $response;

    /**
     * __construct
     *
     * @param object $response Server response
     */
	public function __construct(stdClass $response)
	{
		parent::__construct(@$response->error);
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
