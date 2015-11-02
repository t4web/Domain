<?php

namespace T4webDomain\Service;

use T4webDomain\ErrorAwareTrait;
use T4webDomainInterface\Service\UpdaterInterface;
use T4webDomainInterface\ValidatorInterface;
use T4webDomainInterface\Infrastructure\RepositoryInterface;

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
     * @param ValidatorInterface $validator
     * @param RepositoryInterface $repository
     */
    public function __construct(
        ValidatorInterface $validator,
        RepositoryInterface $repository
    )
    {
        $this->validator = $validator;
        $this->repository = $repository;
    }

    /**
     * @param mixed $id
     * @param array $data
     * @return EntityInterface|void
     */
    public function update($id, array $data)
    {
        if (!$this->validator->isValid($data)) {
            $this->setErrors($this->validator->getMessages());
            return false;
        }

        /** @var CriteriaInterface $criteria */
        $criteria = $this->repository->createCriteria();
        $criteria->equalTo('id', $id);
        $entity = $this->repository->find($criteria);

        if (!$entity) {
            $this->setErrors(array('general' => sprintf("Entity #%s does not found.", $id)));
            return;
        }

        $entity->populate($data);
        $this->repository->add($entity);

        return $entity;
    }
}
