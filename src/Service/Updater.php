<?php

namespace T4webDomain\Service;

use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\UpdaterInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Updater implements UpdaterInterface
{
    use ErrorAwareTrait;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    /**
     * @param ValidatorInterface $validator
     * @param RepositoryInterface $repository
     */
    public function __construct(
        RepositoryInterface $repository,
        EventManagerInterface $eventManager = null
    )
    {
        $this->repository = $repository;
        $this->eventManager = $eventManager;
    }

    /**
     * @param mixed $id
     * @param array $data
     * @return EntityInterface|null
     */
    public function update($id, array $data)
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
            $event = $this->eventManager->createEvent('update.validation', null, $data);
            $this->eventManager->trigger($event);

            $errors = $event->getErrors();
            if (!empty($errors)) {
                $this->setErrors($errors);
                return $entity;
            }

            $data = $event->getData();
        }

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('update.pre', $entity, $data);
            $this->eventManager->trigger($event);
        }

        $entity->populate($data);
        $this->repository->add($entity);

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('update.post', $entity, $data);
            $this->eventManager->trigger($event);
        }

        return $entity;
    }
}