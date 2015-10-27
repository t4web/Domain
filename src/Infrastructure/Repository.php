<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\TableGateway\TableGateway;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityInterface;

class Repository implements RepositoryInterface
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(
        TableGateway $tableGateway,
        Mapper $mapper,
        QueryBuilder $queryBuilder
    )
    {
        $this->tableGateway = $tableGateway;
        $this->mapper = $mapper;
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function add(EntityInterface $entity)
    {

    }

    /**
     * @param EntityInterface $entity
     * @return void
     */
    public function remove(EntityInterface $entity)
    {

    }

    /**
     * @param mixed $criteria
     * @return EntityInterface|null
     */
    public function find($criteria)
    {
        $select = $this->queryBuilder->getSelect($criteria);

        $select->limit(1)->offset(0);
        $result = $this->tableGateway->selectWith($select)->toArray();

        if (!isset($result[0])) {
            return;
        }

        $entity = $this->mapper->fromTableRow($result[0]);

        return $entity;
    }

    /**
     * @param mixed $criteria
     * @return EntityInterface[]
     */
    public function findMany($criteria)
    {

    }

    /**
     * @param mixed $criteria
     * @return int
     */
    public function count($criteria)
    {

    }
}