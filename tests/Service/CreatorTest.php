<?php

namespace T4webDomainTest\Service;

use T4webDomainInterface\EventManagerInterface;
use T4webDomainInterface\EventInterface;
use T4webDomain\Service\Creator;

class CreatorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $repository = $this->prophesize('T4webDomainInterface\Infrastructure\RepositoryInterface');
        $entityFactory = $this->prophesize('T4webDomainInterface\EntityFactoryInterface');
        $eventManager = $this->prophesize(EventManagerInterface::class);

        $creator = new Creator(
            $repository->reveal(),
            $entityFactory->reveal(),
            $eventManager->reveal()
        );

        $data = ['id' => 11];

        $entity = $this->prophesize('T4webDomainInterface\EntityInterface');

        $entityFactory->create($data)->willReturn($entity->reveal());

        $event = $this->prophesize(EventInterface::class);

        $eventManager
            ->createEvent('create.pre', $entity->reveal(), $data)
            ->willReturn($event->reveal());


        $repository->add($entity->reveal());

        $eventManager
            ->createEvent('create.post', $entity->reveal(), $data)
            ->willReturn($event->reveal());
        $eventManager->trigger($event->reveal())->willReturn(null);

        $resultEntity = $creator->handle([], $data);

        $this->assertEquals($entity->reveal(), $resultEntity);
    }
}
