<?php
namespace F3\AppNexusClient;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    private $url;
    private $curl;
    private $http;

    protected function setUp()
    {
        $this->url = 'http://example.com/test';
        $this->curl = $this->getMock('F3\\CurlWrapper\\Curl');
        $this->http = new HttpClient($this->curl);
    }

    public function httpMethods()
    {
        $url = 'http://example.com/test';
        return array(
            array(
                HttpMethod::GET,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => false,
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::POST,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::PUT,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'PUT',
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
            array(
                HttpMethod::DELETE,
                array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_POSTFIELDS => '{"foo":"bar"}',
                    CURLOPT_HTTPHEADER => array(),
                ),
            ),
        );
    }

    private function initCurlMock($options, $response, $contentType = HttpClient::CONTENT_TYPE_JSON)
    {
        $this->curl->expects($this->once())
            ->method('setOptArray')
            ->with($options);
        $this->curl->expects($this->once())
            ->method('exec')
            ->will($this->returnValue($response));
        $this->curl->expects($this->once())
            ->method('getInfo')
            ->with(CURLINFO_CONTENT_TYPE)
            ->will($this->returnValue($contentType));
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCall($method, $options)
    {
        $response = (object) array('status' => 'OK');
        $this->initCurlMock($options, json_encode(array('response' => $response)));
        $this->assertEquals($response, $this->http->call($method, $this->url, array('foo' => 'bar')));
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCallWhenResponseIsNotJSON($method, $options)
    {
        $response ='foo' ;
        $this->initCurlMock($options, $response, 'text/html');
        $this->assertEquals($response, $this->http->call($method, $this->url, array('foo' => 'bar')));
    }

    /**
     * @dataProvider httpMethods
     */
    public function testCallWhenResponseIsJSONWithCharset($method, $options)
    {
        $response = (object) array('status' => 'OK');
        $contentType = 'application/json; charset=utf-8';
        $this->initCurlMock($options, json_encode(array('response' => $response)), $contentType);
        $this->assertEquals($response, $this->http->call($method, $this->url, array('foo' => 'bar')));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException RuntimeException
     * @expectedExceptionMessage Unexpected response: Pew-pew!
     */
    public function testInvalidResponse($method, $options)
    {
        $this->initCurlMock($options, 'Pew-pew!');
        $this->http->call($method, $this->url, array('foo' => 'bar'));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException F3\AppNexusClient\ServerException
     * @expectedExceptionMessage Foo error
     */
    public function testServerError($method, $options)
    {
        $response = (object) array(
            'status' => 'OMG',
            'error' => 'Foo error',
        );
        $this->initCurlMock($options, json_encode(array('response' => $response)));
        $this->http->call($method, $this->url, array('foo' => 'bar'));
    }

    /**
     * @dataProvider httpMethods
     * @expectedException F3\AppNexusClient\TokenExpiredException
     */
    public function testTokenExpired($method, $options)
    {
        $response = (object) array(
            'status' => 'OMG',
            'error_id' => 'NOAUTH',
        );
        $this->initCurlMock($options, json_encode(array('response' => $response)));
        $this->http->call($method, $this->url, array('foo' => 'bar'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid method: BOO
     */
    public function testInvalidMethod()
    {
        $this->http->call('BOO', $this->url);
    }
}
