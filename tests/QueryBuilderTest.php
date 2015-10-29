<?php

namespace T4webDomainTest;

use Zend\Db\Adapter\Adapter;
use T4webDomain\Infrastructure\QueryBuilder;
use T4webDomain\Infrastructure\Criteria;

class QueryBuilderTest extends \PHPUnit_Framework_TestCase
{
    private $dbAdapter;

    public function setUp()
    {
        $this->dbAdapter = new Adapter([
            'driver'         => 'Pdo',
            'dsn'            => 'mysql:dbname=board;host=localhost',
            'driver_options' => array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
            ),
            'username' => 'board',
            'password' => '111',
        ]);
    }

    public function testPredicateGetSelect()
    {
        $qb = new QueryBuilder('users');

        $criteria = new Criteria('users');
        $criteria->equalTo('id', 2);
        $criteria->notEqualTo('id', 3);
        $criteria->lessThan('id', 4);
        $criteria->greaterThan('id', 5);
        $criteria->greaterThanOrEqualTo('id', 6);
        $criteria->lessThanOrEqualTo('id', 7);
        $criteria->like('name', 'php');
        $criteria->isNull('id');
        $criteria->isNotNull('id');
        $criteria->in('type', [1,2,3]);
        $criteria->between('id', 1, 22);
        $criteria->limit(4);
        $criteria->offset(1);
        $criteria->order('id');

        $select = $qb->getSelect($criteria);

        $sql = $select->getSqlString($this->dbAdapter->getPlatform());

        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
        $this->assertEquals(
            "SELECT `users`.* "
            . "FROM `users` "
            . "WHERE `users`.`id` = '2' "
            . "AND `users`.`id` != '3' "
            . "AND `users`.`id` < '4' "
            . "AND `users`.`id` > '5' "
            . "AND `users`.`id` >= '6' "
            . "AND `users`.`id` <= '7' "
            . "AND `users`.`name` LIKE 'php' "
            . "AND `users`.`id` IS NULL "
            . "AND `users`.`id` IS NOT NULL "
            . "AND `users`.`type` IN ('1', '2', '3') "
            . "AND `users`.`id` BETWEEN '1' AND '22' "
            . "ORDER BY `users`.`id` ASC "
            . "LIMIT '4' "
            . "OFFSET '1'",
            $sql
        );
    }

    public function testRelationGetSelect()
    {
        $qb = new QueryBuilder('users');

        $criteria = new Criteria('users');
        $criteria->greaterThan('id', 5);
        $criteria->limit(20);
        $criteria->order('id');
        $criteria->relation('photos')
            ->in('status', [2,3]);

        $select = $qb->getSelect($criteria);

        $sql = $select->getSqlString($this->dbAdapter->getPlatform());

        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
        $this->assertEquals(
            "SELECT `users`.* "
            . "FROM `users` "
            . "INNER JOIN `photos` ON `users`.`id` = `photos`.`user_id` "
            . "WHERE `users`.`id` > '5' "
            . "AND `photos`.`status` IN ('2', '3') "
            . "ORDER BY `users`.`id` ASC "
            . "LIMIT '20'",
            $sql
        );
    }

    public function testOrGetSelect()
    {
        $qb = new QueryBuilder('users');

        $criteria = new Criteria('users');
        $criteria->greaterThan('id', 5);
        $criteria->orCriteria()
            ->lessThan('id', 50)
            ->notEqualTo('id', 40);
        $criteria->limit(20);
        $criteria->order('id');
        $criteria->relation('photos')
            ->in('status', [2,3]);

        $select = $qb->getSelect($criteria);

        $sql = $select->getSqlString($this->dbAdapter->getPlatform());

        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
        $this->assertEquals(
            "SELECT `users`.* "
            . "FROM `users` "
            . "INNER JOIN `photos` ON `users`.`id` = `photos`.`user_id` "
            . "WHERE `users`.`id` > '5' "
            . "OR (`users`.`id` < '50' AND `users`.`id` != '40') "
            . "AND `photos`.`status` IN ('2', '3') "
            . "ORDER BY `users`.`id` ASC "
            . "LIMIT '20'",
            $sql
        );
    }

}
