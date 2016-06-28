<?php
namespace F3\AppNexusClient;

// apc_store and apc_fetch are not working on cli
function apc_store()
{
    return true;
}

function apc_fetch()
{
    return 'bar';
}

class ApcTokenStorageTest extends \PHPUnit_Framework_TestCase
{
    private $storage;

    protected function setUp()
    {
        if ( ! extension_loaded('apc')) {
            //$this->markTestSkipped('APC extension is not loaded');
        }
        $this->storage = new ApcTokenStorage('pref_', 0);
    }

    public function testInterface()
    {
        $this->assertInstanceOf('F3\\AppNexusClient\\TokenStorage', $this->storage);
    }

    public function testSet()
    {
        $this->assertTrue($this->storage->set('foo', 'bar'));
        $this->assertEquals('bar', $this->storage->get('foo'));

    }

    public function testGet()
    {
        $this->assertEquals('bar', $this->storage->get('foo'));
    }
}
