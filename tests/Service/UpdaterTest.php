<?php

namespace T4webDomainTest\Service;

use T4webDomainInterface\EventManagerInterface;
use T4webDomainInterface\EventInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomain\Service\Updater;
use T4webDomain\Exception\EntityNotFoundException;

class UpdaterTest extends \PHPUnit_Framework_TestCase
{
    private $repository;
    private $updater;
    private $eventManager;

    public function setUp()
    {
        $this->repository = $this->prophesize(RepositoryInterface::class);
        $this->eventManager = $this->prophesize(EventManagerInterface::class);

        $this->updater = new Updater(
            $this->repository->reveal(),
            $this->eventManager->reveal()
        );
    }

    public function testUpdate()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $entity = $this->prophesize(EntityInterface::class);
        $criteria = $this->prophesize(CriteriaInterface::class);
        $event = $this->prophesize(EventInterface::class);

        $this->repository->createCriteria(['id.equalTo' => $id])
            ->willReturn($criteria->reveal());

        $this->repository->find($criteria)
            ->willReturn($entity->reveal());

        $this->eventManager
            ->createEvent('update.pre', $entity->reveal(), $data)
            ->willReturn($event->reveal());

        $entity->populate($data)->willReturn(null);

        $this->repository->add($entity->reveal())->willReturn(null);

        $this->eventManager
            ->createEvent('update.post', $entity->reveal(), $data)
            ->willReturn($event->reveal());
        $this->eventManager->trigger($event->reveal())->willReturn(null);


        $resultEntity = $this->updater->handle(['id.equalTo' => $id], $data);

        $this->assertEquals($entity->reveal(), $resultEntity);
    }

    public function testUpdateNotFound()
    {
        $id = 11;
        $data = ['name' => 'Some name'];

        $criteria = $this->prophesize(CriteriaInterface::class);

        $this->repository->createCriteria(['id.equalTo' => $id])
            ->willReturn($criteria->reveal());

        $this->repository->find($criteria)
            ->willReturn(null);

        $this->setExpectedException(EntityNotFoundException::class);

        $result = $this->updater->handle(['id.equalTo' => $id], $data);

        $this->assertNull($result);
    }
}
