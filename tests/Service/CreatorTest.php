<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Creator;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    private $validatorMock;
    private $repositoryMock;
    private $entityFactoryMock;
    private $creator;

    public function setUp()
    {
        $this->validatorMock = $this->getMock('T4webDomainInterface\ValidatorInterface');
        $this->repositoryMock = $this->getMock('T4webDomainInterface\Infrastructure\RepositoryInterface');
        $this->entityFactoryMock = $this->getMock('T4webDomainInterface\EntityFactoryInterface');

        $this->creator = new Creator(
            $this->validatorMock,
            $this->repositoryMock,
            $this->entityFactoryMock
        );
    }

    public function testCreate()
    {
        $data = ['id' => 11];

        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($data))
            ->will($this->returnValue(true));

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
        $data = ['id' => 11];

        $this->validatorMock->expects($this->once())
            ->method('isValid')
            ->with($this->equalTo($data))
            ->will($this->returnValue(false));

        $this->validatorMock->expects($this->once())
            ->method('getMessages')
            ->will($this->returnValue(['field' => 'Error message']));

        $this->repositoryMock->expects($this->never())
            ->method('add');

        $result = $this->creator->create($data);

        $this->assertNull($result);
    }
}
