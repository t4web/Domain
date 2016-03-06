<?php

namespace T4webDomainTest\Service;

use T4webDomainInterface\EventManagerInterface;
use T4webDomainInterface\EventInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
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
        $event = $this->prophesize(EventInterface::class);

        $this->repository->createCriteria(['id.equalTo' => $id])
            ->willReturn($criteria->reveal());

        $this->repository->find($criteria)
            ->willReturn($entity->reveal());

        $this->eventManager
            ->createEvent('delete.pre', $entity->reveal())
            ->willReturn($event->reveal());

        $this->repository->remove($entity->reveal())
            ->willReturn(null);

        $this->eventManager
            ->createEvent('delete.post', $entity->reveal())
            ->willReturn($event->reveal());
        $this->eventManager->trigger($event->reveal())->willReturn(null);

        $resultEntity = $this->deleter->handle(['id.equalTo' => $id], []);

        $this->assertEquals($entity->reveal(), $resultEntity);
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
