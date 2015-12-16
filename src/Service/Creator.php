<?php

namespace T4webDomain\Service;

use T4webDomain\Event;
use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\CreatorInterface;
use T4webDomainInterface\ValidatorInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityFactoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Creator implements CreatorInterface
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
     * @var EntityFactoryInterface
     */
    protected $entityFactory;

    /**
     * @var EventManagerInterface
     */
    protected $eventManager;

    public function __construct(
        ValidatorInterface $validator,
        RepositoryInterface $repository,
        EntityFactoryInterface $entityFactory,
        EventManagerInterface $eventManager = null
    ) {

        $this->validator = $validator;
        $this->repository = $repository;
        $this->entityFactory = $entityFactory;
        $this->eventManager = $eventManager;
    }

    public function create(array $data)
    {
        if (!$this->validator->isValid($data)) {
            $this->setErrors($this->validator->getMessages());
            return;
        }

        $entity = $this->entityFactory->create($data);

        if ($this->eventManager) {
            $event = new Event('create.pre', $entity);
            $this->eventManager->trigger($event);
        }

        $this->repository->add($entity);

        if ($this->eventManager) {
            $event = new Event('create.post', $entity);
            $this->eventManager->trigger($event);
        }

        return $entity;
    }
}
