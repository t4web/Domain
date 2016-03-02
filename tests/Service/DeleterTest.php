<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Deleter;
use T4webDomain\Exception\EntityNotFoundException;

class DeleterTest extends \PHPUnit_Framework_TestCase
{
    private $repositoryMock;
    private $deleter;

    public function setUp()
    {
        $this->repositoryMock = $this->getMock('T4webDomainInterface\Infrastructure\RepositoryInterface');

        $this->deleter = new Deleter(
            $this->repositoryMock
        );
    }

    public function testDelete()
    {
        $id = 11;

        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');
        $entityMock = $this->getMock('T4webDomainInterface\EntityInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->with($this->equalTo(['id.equalTo' => $id]))
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue($entityMock));

        $this->repositoryMock->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($entityMock));

        $entity = $this->deleter->handle(['id.equalTo' => $id], []);

        $this->assertEquals($entityMock, $entity);
    }

    public function testDeleteNotExistEntry()
    {
        $id = 11;

        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');
        $entityMock = $this->getMock('T4webDomainInterface\EntityInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->with($this->equalTo(['id.equalTo' => $id]))
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue(null));

        $this->repositoryMock->expects($this->never())
            ->method('remove')
            ->with($this->equalTo($entityMock));

        $this->setExpectedException(EntityNotFoundException::class);

        $result = $this->deleter->handle(['id.equalTo' => $id], []);

        $this->assertNull($result);
    }

    public function testDeleteAll()
    {
        $filter = ['status' => 3];

        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');
        $entityMock1 = $this->getMock('T4webDomainInterface\EntityInterface');
        $entityMock2 = $this->getMock('T4webDomainInterface\EntityInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->with($this->equalTo($filter))
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('findMany')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue([$entityMock1, $entityMock2]));

        $this->repositoryMock->expects($this->at(2))
            ->method('remove')
            ->with($this->equalTo($entityMock1));

        $this->repositoryMock->expects($this->at(3))
            ->method('remove')
            ->with($this->equalTo($entityMock1));

        $result = $this->deleter->deleteAll($filter);

        $this->assertEquals([$entityMock1, $entityMock2], $result);
    }

    public function testDeleteAllWithEmptyResult()
    {
        $filter = ['status' => 3];

        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');
        $entityMock1 = $this->getMock('T4webDomainInterface\EntityInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->with($this->equalTo($filter))
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('findMany')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue([]));

        $this->setExpectedException(EntityNotFoundException::class);

        $this->repositoryMock->expects($this->never())
            ->method('remove')
            ->with($this->equalTo($entityMock1));

        $result = $this->deleter->deleteAll($filter);

        $this->assertNull($result);
    }
}
