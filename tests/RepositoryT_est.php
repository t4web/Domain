<?php

namespace T4webDomainTest;

use T4webDomain\Entity;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\EventManager\EventManager;
use T4webDomain\Infrastructure\Repository;
use T4webDomain\Infrastructure\Mapper;
use T4webDomain\Infrastructure\QueryBuilder;
use T4webDomain\Infrastructure\Criteria;
use T4webDomain\Infrastructure\IdentityMap;
use T4webDomain\EntityFactory;

class Task extends Entity
{
    protected $name;
    protected $assignee;
}

class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Adapter
     */
    private $repository;

    public function setUp()
    {
        $dbAdapter = new Adapter([
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=board;host=localhost',
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ),
            'username' => 'board',
            'password' => '111',
        ]);

        $tableGateway = new TableGateway('tasks', $dbAdapter);
        $mapper = new Mapper(
            [
                'id' => 'id',
                'name' => 'name',
                'assignee' => 'assignee',
                'status' => 'status',
                'type' => 'type',
            ],
            new EntityFactory('T4webDomainTest\Task', 'ArrayObject'));
        $queryBuilder = new QueryBuilder('tasks');

        $em = new EventManager();

        $this->repository = new Repository(
            $tableGateway,
            $mapper,
            $queryBuilder,
            $em
        );
    }

    public function testFindRowExists()
    {
        $id = 2;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $this->assertInstanceOf('T4webDomainTest\Task', $entity);
        $this->assertEquals($id, $entity->getId());
    }

    public function testFindRowNotExists()
    {
        $id = 1;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $this->assertNull($entity);
    }

    public function testCount()
    {
        $id = 2;

        $count = $this->repository->count(new Criteria('id', 'equalTo', $id));

        $this->assertEquals(1, $count);
    }

    public function testFindManyRowExists()
    {
        $id = 2;

        $entities = $this->repository->findMany(new Criteria('id', 'equalTo', $id));

        $this->assertInstanceOf('ArrayObject', $entities);
        $this->assertEquals($id, $entities[$id]->getId());
    }

    public function testAddInsert()
    {
        $newEntity = $this->repository->add(new Task(['name' => 'Some name', 'assignee' => 'AA']));

        $this->assertInstanceOf('T4webDomainTest\Task', $newEntity);

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $newEntity->getId()));

        $this->assertInstanceOf('T4webDomainTest\Task', $entity);
        $this->assertEquals($newEntity->getId(), $entity->getId());
    }

    public function testAddUpdate()
    {
        $id = 3;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $this->assertInstanceOf('T4webDomainTest\Task', $entity);

        $entity->populate(['name' => date('His'), 'assignee' => date('is')]);

        $rowsAffected = $this->repository->add($entity);

        $this->assertEquals(1, $rowsAffected);
    }
/*
    public function testRemove()
    {
        $id = 4;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $rowsAffected = $this->repository->remove($entity);

        $this->assertEquals(1, $rowsAffected);
    }
*/
}
