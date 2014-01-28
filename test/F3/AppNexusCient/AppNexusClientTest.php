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
				CURLOPT_HTTPHEADER => array(),
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

	public function testCall()
	{
		$curl = $this->getMock('F3\\CurlWrapper\\Curl');

		$storage = $this->getMock('F3\\AppNexusClient\\TokenStorage');
		$storage->expects($this->once())
			->method('get')
			->with('user')
			->will($this->returnValue(null));
		$storage->expects($this->once())
			->method('set')
			->with('user', 'my_token');

		$client = $this->getMock('F3\\AppNexusClient\\AppNexusClient', array('getNewToken'), array('user', 'pass', 'http://example.com', $curl, $storage));

		$client->expects($this->once())
			->method('getNewToken')
			->will($this->returnValue('my_token'));

		$curl->expects($this->once())
			->method('setOptArray')
			->with(array(
				CURLOPT_URL => 'http://example.com/my_path',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_CUSTOMREQUEST => 'PUT',
				CURLOPT_POSTFIELDS => '{"my_foo":"my_bar"}',
				CURLOPT_HTTPHEADER => array(
					'Authorization: my_token',
				),
			));

		$curl->expects($this->once())
			->method('exec')
			->will($this->returnValue(json_encode(array(
				'response' => array(
					'status' => 'OK',
					'foo' => 'bar',
				),
			))));

		$this->assertEquals((object) array('status' => 'OK', 'foo' => 'bar'), $client->call('PUT', '/my_path', array('my_foo' => 'my_bar')));
	}
}
