<?php

namespace T4webDomain\Service;

use T4webDomainInterface\Service\FinderInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityInterface;

class Finder implements FinderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param array $filter
     * @return EntityInterface|null
     */
    public function find(array $filter)
    {
        $criteria = $this->repository->createCriteria($filter);
        return $this->repository->find($criteria);
    }

    /**
     * @param array $filter
     * @return EntityInterface[]
     */
    public function findMany(array $filter = array())
    {
        $criteria = $this->repository->createCriteria($filter);
        return $this->repository->findMany($criteria);
    }

    /**
     * @param array $filter
     * @return int
     */
    public function count(array $filter)
    {
        $criteria = $this->repository->createCriteria($filter);
        return $this->repository->count($criteria);
    }

    /**
     * @param mixed $id
     * @return EntityInterface|null
     */
    public function getById($id)
    {
        $criteria = $this->repository->createCriteria();
        $criteria->equalTo('id', $id);
        return $this->repository->find($criteria);
    }

    /**
     * @param array $ids
     * @return EntityInterface[]
     */
    public function getByIds(array $ids)
    {
        $criteria = $this->repository->createCriteria();
        $criteria->in('id', $ids);
        return $this->repository->find($criteria);
    }
}
