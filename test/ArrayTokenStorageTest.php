<?php
namespace F3\AppNexusClient;

class ArrayTokenStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGet()
    {
        $storage = new ArrayTokenStorage();
        $this->assertInstanceOf('F3\\AppNexusClient\\TokenStorage', $storage);
        $this->assertFalse($storage->get('foo'));
        $storage->set('foo', 'bar');
        $this->assertEquals('bar', $storage->get('foo'));
    }
}
