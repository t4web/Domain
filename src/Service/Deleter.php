<?php

namespace T4webDomain\Service;

use T4webDomain\Event;
use T4webDomain\Exception\EntityNotFoundException;
use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\ServiceInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Deleter implements ServiceInterface
{
    /**
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository, EventManagerInterface $eventManager = null)
    {
        $this->repository = $repository;
        $this->eventManager = $eventManager;
    }

    /**
     * @return EntityInterface
     */
    public function handle($filter, $changes)
    {
        /** @var CriteriaInterface $criteria */
        $criteria = $this->repository->createCriteria($filter);
        $entity = $this->repository->find($criteria);

        if (!$entity) {
            throw new EntityNotFoundException("Entity does not found.");
        }

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('delete.pre', $entity);
            $this->eventManager->trigger($event);
        }

        $this->repository->remove($entity);

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('delete.post', $entity);
            $this->eventManager->trigger($event);
        }

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
            throw new EntityNotFoundException("Entities does not found.");
        }

        foreach ($entities as $entity) {
            $this->repository->remove($entity);
        }

        return $entities;
    }
}
