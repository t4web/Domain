<?php

namespace T4webDomain\Service;

use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\CreatorInterface;
use T4webDomainInterface\ValidatorInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityFactoryInterface;

class Creator implements CreatorInterface
{
    use ErrorAwareTrait

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

    public function __construct(
        ValidatorInterface $validator,
        RepositoryInterface $repository,
        EntityFactoryInterface $entityFactory
    ) {

        $this->validator = $validator;
        $this->repository = $repository;
        $this->entityFactory = $entityFactory;
    }

    public function create(array $data)
    {
        if (!$this->validator->isValid($data)) {
            $this->setErrors($this->validator->getMessages());
            return false;
        }

        $entity = $this->entityFactory->create($data);
        $this->repository->add($entity);

        return $entity;
    }
}
