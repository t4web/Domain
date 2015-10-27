<?php

namespace T4webDomain\Infrastructure;

use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityInterface;

class Repository implements RepositoryInterface
{
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
     * @return EntityInterface
     */
    public function find($criteria)
    {
        $select = $queryBuilder->getSelect($criteria);
        $rows = $this->tableGateway->selectWidth($select);
    }

    /**
     * @param mixed $criteria
     * @return array of EntityInterface
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