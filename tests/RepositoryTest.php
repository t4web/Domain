<?php

namespace T4webDomainTest;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use T4webDomain\Infrastructure\Repository;
use T4webDomain\Infrastructure\Mapper;
use T4webDomain\Infrastructure\QueryBuilder;
use T4webDomain\Infrastructure\Criteria;
use T4webDomain\EntityFactory;

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
            new EntityFactory('T4webDomain\Entity', 'ArrayObject'));
        $queryBuilder = new QueryBuilder('tasks');

        $this->repository = new Repository($tableGateway, $mapper, $queryBuilder);
    }

    public function testFindRowExists()
    {
        $id = 2;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $this->assertInstanceOf('T4webDomain\Entity', $entity);
        $this->assertEquals($id, $entity->getId());
    }

    public function testFindRowNotExists()
    {
        $id = 1;

        $entity = $this->repository->find(new Criteria('id', 'equalTo', $id));

        $this->assertNull($entity);
    }
}
