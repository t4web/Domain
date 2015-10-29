<?php

namespace T4webDomain\Service;

use T4webDomain\ErrorAwareTrait;
use T4webDomain\Infrastructure\Criteria;
use T4webDomainInterface\CriteriaInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\Service\DeleterInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomain\Infrastructure\CriteriaFactory;

class Deleter implements DeleterInterface
{
    use ErrorAwareTrait;

    /**
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     *
     * @var CriteriaFactory
     */
    protected $criteriaFactory;

    /**
     * @param RepositoryInterface $repository
     * @param CriteriaFactory $criteriaFactory
     */
    public function __construct(
        RepositoryInterface $repository,
        CriteriaFactory $criteriaFactory
    )
    {
        $this->repository = $repository;
        $this->criteriaFactory = $criteriaFactory;
    }

    /**
     * @param int $id
     * @return EntityInterface|null
     */
    public function delete($id)
    {
        /** @var CriteriaInterface $criteria */
        $criteria = $this->repository->createCriteria();
        $criteria->equalTo('id', $id);
        $entity = $this->repository->find($criteria);

        if (!$entity) {
            $this->setErrors(array('general' => sprintf("Entity #%s does not found.", $id)));
            return;
        }

        $this->repository->remove($entity);

        return $entity;
    }

    /**
     * @param array $filter
     * @return EntityInterface[]|null
     */
    public function deleteAll(array $filter = [])
    {
        $criteria = $this->repository->createCriteria($filter);
        $entities = $this->repository->findMany($criteria);

        if (empty($entities)) {
            $this->setErrors(array('general' => 'Entities does not found.'));
            return;
        }

        foreach ($entities as $entity) {
            $this->repository->remove($entity);
        }

        return $entities;
    }
}
