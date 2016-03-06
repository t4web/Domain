<?php

namespace T4webDomainTest\Service;

use T4webDomain\Service\Deleter;
use T4webDomain\Exception\EntityNotFoundException;

class DeleterTest extends \PHPUnit_Framework_TestCase
{
    private $repository;
    private $deleter;
    private $eventManager;

    public function setUp()
    {
        $this->repository = $this->prophesize(RepositoryInterface::class);
        $this->eventManager = $this->prophesize(EventManagerInterface::class);

        $this->deleter = new Deleter(
            $this->repository->reveal(),
            $this->eventManager->reveal()
        );
    }

    public function testDelete()
    {
        $id = 11;

        $entity = $this->prophesize(EntityInterface::class);
        $criteria = $this->prophesize(CriteriaInterface::class);

        $this->repository->createCriteria(['id.equalTo' => $id])
            ->willReturn($criteria->reveal());

        $this->repository->find($criteria)
            ->willReturn($entity->reveal());

        $this->repositoryMock->remove($entity->reveal())
            ->willReturn(null);

        $resultEntity = $this->deleter->handle(['id.equalTo' => $id], []);

        $this->assertEquals($entity, $resultEntity);
    }

    public function testDeleteNotExistEntry()
    {
        $id = 11;

        $criteria = $this->prophesize(CriteriaInterface::class);

        $this->repository->createCriteria(['id.equalTo' => $id])
            ->willReturn($criteria->reveal());

        $this->repository->find($criteria)
            ->willReturn(null);

        $this->setExpectedException(EntityNotFoundException::class);

        $result = $this->deleter->handle(['id.equalTo' => $id], []);

        $this->assertNull($result);
    }
}
