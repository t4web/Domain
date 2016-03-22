<?php

namespace T4webDomainTest;

use T4webDomain\EntityFactory;

class EntityFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $factory;

    public function setUp()
    {
        $this->factory = new EntityFactory('T4webDomain\Entity', 'ArrayObject');
    }

    public function testConstruct()
    {
        $this->assertAttributeEquals('T4webDomain\Entity', 'entityClass', $this->factory);
        $this->assertAttributeEquals('ArrayObject', 'collectionClass', $this->factory);
    }

    public function testCreate()
    {
        $entity = $this->factory->create(['id' => 11]);

        $this->assertInstanceOf('T4webDomainInterface\EntityInterface', $entity);
        $this->assertEquals(11, $entity->getId());
    }

    public function testCreateCollection()
    {
        $collection = $this->factory->createCollection(
            [
                ['id' => 11],
                ['id' => 22],
                ['id' => 33],
            ]
        );

        $this->assertInstanceOf('ArrayObject', $collection);
        $this->assertInstanceOf('T4webDomainInterface\EntityInterface', $collection[11]);
        $this->assertInstanceOf('T4webDomainInterface\EntityInterface', $collection[22]);
        $this->assertInstanceOf('T4webDomainInterface\EntityInterface', $collection[33]);
        $this->assertEquals(11, $collection[11]->getId());
        $this->assertEquals(22, $collection[22]->getId());
        $this->assertEquals(33, $collection[33]->getId());
    }

    public function testAggregateCreation()
    {
        $factory = new EntityFactory('T4webDomainTest\Assets\ComplexTask', 'ArrayObject');

        $assignee = new \stdClass();
        $project = new \stdClass();

        $entity = $factory->create([
            'data' => ['id' => 11],
            'aggregateItems' => [$assignee, $project],
        ]);

        $this->assertSame($assignee, $entity->getAssignee());
        $this->assertSame($project, $entity->getProject());
    }
}
