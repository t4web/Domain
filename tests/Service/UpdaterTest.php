<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Updater;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    private $validatorMock;
    private $repositoryMock;
    private $updater;

    public function setUp()
    {
        $this->validatorMock = $this->getMock('T4webDomainInterface\ValidatorInterface');
        $this->repositoryMock = $this->getMock('T4webDomainInterface\Infrastructure\RepositoryInterface');

        $this->updater = new Updater(
            $this->validatorMock,
            $this->repositoryMock
        );
    }

    public function testUpdate()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($data))
            ->will($this->returnValue(true));

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

    public function testCreateNotValid()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($data))
            ->will($this->returnValue(false));

        $this->validatorMock->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue(['field' => 'Error message']));

        $this->repositoryMock->expects($this->never())
            ->method('add');

        $result = $this->updater->update($id, $data);

        $this->assertFalse($result);
    }
}
