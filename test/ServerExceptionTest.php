<?php
namespace F3\AppNexusClient;

use stdClass;

class ServerExceptionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetResponse()
    {
        $response = new stdClass;

        $e = new ServerException($response);
        $this->assertSame($response, $e->getResponse());
    }

    public function testMessage()
    {
        $response = new stdClass;
        $response->error = 'foo';

        $e = new ServerException($response);
        $this->assertEquals('foo', $e->getMessage());
    }
}
