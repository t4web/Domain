<?php

namespace T4webDomain\Service;

use T4webDomainInterface\ServiceInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomainInterface\EventManagerInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomain\Exception\EntityNotFoundException;

class Updater implements ServiceInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @param RepositoryInterface $repository
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        RepositoryInterface $repository,
        EventManagerInterface $eventManager = null
    ) {

        $this->repository = $repository;
        $this->eventManager = $eventManager;
    }

    /**
     * @return EntityInterface|null
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
            $event = $this->eventManager->createEvent('update.pre', $entity, $changes);
            $this->eventManager->trigger($event);
        }

        $entity->populate($changes);
        $this->repository->add($entity);

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('update.post', $entity, $changes);
            $this->eventManager->trigger($event);
        }

        return $entity;
    }
}
