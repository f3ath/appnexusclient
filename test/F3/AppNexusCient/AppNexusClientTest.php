<?php
namespace F3\AppNexusClient;
class AppNexusClientTest extends \PHPUnit_Framework_TestCase
{
	public function testGetNewToken()
	{
		$curl = $this->getMock('F3\\CurlWrapper\\Curl');
		$storage = $this->getMock('F3\\AppNexusClient\\TokenStorage');
		$c = new AppNexusClient('user', 'pass', 'http://example.com', $curl, $storage);

		$curl->expects($this->once())
			->method('setOptArray')
			->with(array(
				CURLOPT_URL => 'http://example.com/auth',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => '{"auth":{"username":"user","password":"pass"}}',
			));
		$curl->expects($this->once())
			->method('exec')
			->will($this->returnValue(json_encode(array(
				'response' => array(
					'status' => 'OK',
					'token' => 'my_token',

				),
			))));

		$this->assertEquals('my_token', $c->getNewToken());
	}
}
