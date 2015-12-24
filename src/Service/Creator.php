<?php

namespace T4webDomain\Service;

use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\CreatorInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityFactoryInterface;
use T4webDomainInterface\EventManagerInterface;

class Creator implements CreatorInterface
{
    use ErrorAwareTrait;

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

    /**
     * @param RepositoryInterface $repository
     * @param EntityFactoryInterface $entityFactory
     * @param EventManagerInterface|null $eventManager
     */
    public function __construct(
        RepositoryInterface $repository,
        EntityFactoryInterface $entityFactory,
        EventManagerInterface $eventManager = null
    ) {
        $this->repository = $repository;
        $this->entityFactory = $entityFactory;
        $this->eventManager = $eventManager;
    }

    public function create(array $data)
    {
        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('create.validation', null, $data);
            $this->eventManager->trigger($event);

            $errors = $event->getErrors();
            if (!empty($errors)) {
                $this->setErrors($errors);
                return;
            }

            $data = $event->getValidData();
        }

        $entity = $this->entityFactory->create($data);

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('create.pre', $entity);
            $this->eventManager->trigger($event);
        }

        $this->repository->add($entity);

        if ($this->eventManager) {
            $event = $this->eventManager->createEvent('create.post', $entity);
            $this->eventManager->trigger($event);
        }

        return $entity;
    }
}
