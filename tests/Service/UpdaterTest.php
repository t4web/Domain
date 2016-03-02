<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Updater;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    private $repositoryMock;
    private $updater;

    public function setUp()
    {
        $this->repositoryMock = $this->getMock('T4webDomainInterface\Infrastructure\RepositoryInterface');

        $this->updater = new Updater(
            $this->repositoryMock
        );
    }

    public function testUpdate()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $entityMock = $this->getMock('T4webDomainInterface\EntityInterface');
        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue($entityMock));

        $entity = $this->updater->update($id, $data);

        $this->assertEquals($entityMock, $entity);
    }

    public function testUpdateNotFound()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $criteriaMock = $this->getMock('T4webDomainInterface\Infrastructure\CriteriaInterface');

        $this->repositoryMock->expects($this->once())
            ->method('createCriteria')
            ->will($this->returnValue($criteriaMock));

        $this->repositoryMock->expects($this->once())
            ->method('find')
            ->with($this->equalTo($criteriaMock))
            ->will($this->returnValue(null));

        $this->repositoryMock->expects($this->never())
            ->method('add');

        $result = $this->updater->update($id, $data);

        $this->assertNull($result);
    }
}
