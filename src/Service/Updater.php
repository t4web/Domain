<?php

namespace T4webDomain\Service;

use T4webDomain\Event;
use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\UpdaterInterface;
use T4webDomainInterface\ValidatorInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Updater implements UpdaterInterface
{
    use ErrorAwareTrait;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

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
        ValidatorInterface $validator,
        RepositoryInterface $repository,
        EventManagerInterface $eventManager = null
    )
    {
        $this->validator = $validator;
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

        if (!$this->validator->isValid($data)) {
            $this->setErrors($this->validator->getMessages());
            return $entity;
        }

        if ($this->eventManager) {
            $event = new Event('update.pre', $entity, $data);
            $this->eventManager->trigger($event);
        }

        $entity->populate($data);
        $this->repository->add($entity);

        if ($this->eventManager) {
            $event = new Event('update.post', $entity, $data);
            $this->eventManager->trigger($event);
        }

        return $entity;
    }
}