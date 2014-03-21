<?php
namespace F3\AppNexusClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $curl;
    private $http;

    protected function setUp()
    {
        $this->curl = $this->getMock('F3\\CurlWrapper\\Curl');
        $this->http = new HttpClient($this->curl);
    }

    public function httpMethods()
    {
        return array(
            array(
                HttpMethod::GET,
                array(
                    CURLOPT_URL => 'http://example.com/test',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => false,
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::POST,
                array(
                    CURLOPT_URL => 'http://example.com/test',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::PUT,
                array(
                    CURLOPT_URL => 'http://example.com/test',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::DELETE,
                array(
                    CURLOPT_URL => 'http://example.com/test',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
        );
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCall($method, $options)
    {
        $this->curl->expects($this->once())
            ->method('setOptArray')
            ->with($options);
        $response = (object) array('status' => 'OK');
        $this->curl->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode(array(
                'response' => $response,
            ))));
        $this->assertEquals($response, $this->http->call($method, 'http://example.com/test', array('foo' => 'bar')));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unexpected response: Pew-pew!
     */
    public function testInvalidResponse($method, $options)
    {
        $this->curl->expects($this->once())
            ->method('setOptArray')
            ->with($options);
        $response = (object) array('oops');
        $this->curl->expects($this->once())
            ->method('exec')
            ->will($this->returnValue('Pew-pew!'));
        $this->http->call($method, 'http://example.com/test', array('foo' => 'bar'));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException F3\AppNexusClient\ServerException
     * @expectedExceptionMessage Foo error
     */
    public function testServerError($method, $options)
    {
        $this->curl->expects($this->once())
            ->method('setOptArray')
            ->with($options);
        $response = (object) array(
            'status' => 'OMG',
            'error' => 'Foo error',
        );
        $this->curl->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode(array(
                'response' => $response,
            ))));
        $this->http->call($method, 'http://example.com/test', array('foo' => 'bar'));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException F3\AppNexusClient\TokenExpiredException
     */
    public function testTokenExpired($method, $options)
    {
        $this->curl->expects($this->once())
            ->method('setOptArray')
            ->with($options);
        $response = (object) array(
            'status' => 'OMG',
            'error_id' => 'NOAUTH',
        );
        $this->curl->expects($this->once())
            ->method('exec')
            ->will($this->returnValue(json_encode(array(
                'response' => $response,
            ))));
        $this->http->call($method, 'http://example.com/test', array('foo' => 'bar'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid method: BOO
     */
    public function testInvalidMethod()
    {
        $this->http->call('BOO', 'http://example.com/test');
    }
}
