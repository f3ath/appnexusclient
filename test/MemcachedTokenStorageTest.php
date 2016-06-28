<?php
namespace F3\AppNexusClient;

class MemcachedTokenStorageTest extends \PHPUnit_Framework_TestCase
{
    private $mc;
    private $storage;

    protected function setUp()
    {
        $this->mc = $this->getMockBuilder('Memcached')
            ->setMethods(array('set', 'get'))
            ->getMock();
        $this->storage = new MemcachedTokenStorage($this->mc, 'pref_');
    }

    public function testInterface()
    {
        $this->assertInstanceOf('F3\\AppNexusClient\\TokenStorage', $this->storage);
    }

    public function testSet()
    {
        $this->mc->expects($this->once())
            ->method('set')
            ->with('pref_foo', 'bar');
        $this->storage->set('foo', 'bar');
    }

    public function testGet()
    {
        $this->mc->expects($this->once())
            ->method('get')
            ->with('pref_foo')
            ->will($this->returnValue('bar'));
        $this->assertEquals('bar', $this->storage->get('foo'));
    }
}
