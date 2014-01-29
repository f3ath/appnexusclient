<?php
namespace F3\AppNexusClient;

Interface Curl
{
	/**
	 * Initialize a new curl handler
	 * @see curl_init()
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Set curl options
	 * @see curl_setopt_array()
	 *
	 * @param array $options
	 * @return void
	 */
	public function setOptArray(array $options);

	/**
	 * Execute, return the result
	 * @see curl_exec()
	 *
	 * @return mixed
	 */
	public function exec();
}
