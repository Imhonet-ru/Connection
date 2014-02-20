<?php

namespace Imhonet\Connection\Test\Query\Memcached;

use Imhonet\Connection\Query\Memcached\Set;
use Imhonet\Connection\Resource\IResource;

class SetTest extends \PHPUnit_Framework_TestCase
{
    private $data = array(
        'false' => false,
        'true' => true,
        'null' => null,
        'int' => 42,
        'string' => 'forty two',
        'arr' => array(42),
    );

    /**
     * @var Set
     */
    private $query;

    protected function setUp()
    {
        $this->query = new Set();
        $this->query->setData($this->data);
    }

    public function testCreate()
    {
        $this->assertInstanceOf('Imhonet\Connection\Query\Base', $this->query);
    }

    public function testExecute()
    {
        $this->query->setResource($this->getResource());
        $this->assertEquals(true, $this->query->execute());
        $this->assertEquals(0, $this->query->getErrorCode());
    }

    public function testFailure()
    {
        $this->query->setResource($this->getResourceFailed());
        $this->assertEquals(false, $this->query->execute());
        $this->assertNotEquals(0, $this->query->getErrorCode());
    }

    /**
     * @return IResource
     */
    private function getResource()
    {
        $memcached = $this->getMock('\Memcached', array('setMulti'));
        $memcached
            ->expects($this->any())
            ->method('setMulti')
            ->with($this->data)
            ->will($this->returnValue(true))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\Memcached', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($memcached))
        ;

        return $resource;
    }

    /**
     * @return IResource
     */
    private function getResourceFailed()
    {
        $memcached = $this->getMock('\Memcached', array('setMulti', 'getResultCode'));
        $memcached
            ->expects($this->any())
            ->method('setMulti')
            ->withAnyParameters()
            ->will($this->returnValue(false))
        ;
        $memcached
            ->expects($this->any())
            ->method('getResultCode')
            ->will($this->returnValue(\Memcached::RES_FAILURE))
        ;

        $resource = $this->getMock('Imhonet\Connection\Resource\Memcached', array('getHandle'));
        $resource
            ->expects($this->any())
            ->method('getHandle')
            ->will($this->returnValue($memcached))
        ;

        return $resource;
    }

    protected function tearDown()
    {
        $this->query = null;
    }

}