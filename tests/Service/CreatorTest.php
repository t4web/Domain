<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Creator;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    private $repositoryMock;
    private $entityFactoryMock;
    private $creator;

    public function setUp()
    {
        $this->repositoryMock = $this->getMock('T4webDomainInterface\Infrastructure\RepositoryInterface');
        $this->entityFactoryMock = $this->getMock('T4webDomainInterface\EntityFactoryInterface');

        $this->creator = new Creator(
            $this->repositoryMock,
            $this->entityFactoryMock
        );
    }

    public function testCreate()
    {
        $data = ['id' => 11];

        $entityMock = $this->getMock('T4webDomainInterface\EntityInterface');

        $this->entityFactoryMock->expects($this->once())
            ->method('create')
            ->with($this->equalTo($data))
            ->will($this->returnValue($entityMock));

        $this->repositoryMock->expects($this->once())
            ->method('add')
            ->with($this->equalTo($entityMock));

        $entity = $this->creator->create($data);

        $this->assertEquals($entityMock, $entity);
    }

    public function testCreateNotValid()
    {
        $this->markTestIncomplete();
        return;

        $data = ['id' => 11];

        $this->repositoryMock->expects($this->never())
            ->method('add');

        $result = $this->creator->create($data);

        $this->assertNull($result);
    }
}
