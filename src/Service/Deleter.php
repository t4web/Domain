<?php

namespace T4webDomain\Service;

use T4webDomain\Event;
use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\Service\DeleterInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Deleter implements DeleterInterface
{
    use ErrorAwareTrait;

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

        if ($this->eventManager) {
            $event = new Event('delete.pre', $entity);
            $this->eventManager->trigger($event);
        }

        $this->repository->remove($entity);

        if ($this->eventManager) {
            $event = new Event('delete.post', $entity);
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
            $this->setErrors(array('general' => 'Entities does not found.'));
            return;
        }

        foreach ($entities as $entity) {
            $this->repository->remove($entity);
        }

        return $entities;
    }
}
