<?php

namespace T4webDomain\Service;

use T4webDomainInterface\Service\UpdaterInterface;

class Updater implements UpdaterInterface
{
    use ErrorAwareTrait;

    /**
     * @var InputFilterInterface
     */
    protected $inputFilter;

    /**
     * @var \T4webBase\Domain\Repository\DbRepository
     */
    protected $repository;

    /**
     * @var \T4webBase\Domain\Criteria\Factory
     */
    protected $criteriaFactory;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @param InputFilterInterface $inputFilter
     * @param DbRepository $repository
     * @param CriteriaFactory $criteriaFactory
     * @param EventManager|null $eventManager
     */
    public function __construct(
        InputFilterInterface $inputFilter,
        DbRepository $repository,
        CriteriaFactory $criteriaFactory,
        EventManager $eventManager = null)
    {

        $this->inputFilter = $inputFilter;
        $this->repository = $repository;
        $this->criteriaFactory = $criteriaFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isValid(array $data)
    {
        $this->inputFilter->setData($data);
        if (!$this->inputFilter->isValid()) {
            $this->setErrors($this->inputFilter->getMessages());
            return false;
        }
        return true;
    }

    /**
     * @param $id
     * @param array $data
     * @return EntityInterface|void
     */
    public function update($id, array $data)
    {

        $this->entity = $this->repository->find($this->criteriaFactory->getNativeCriteria('Id', $id));
        if (!$this->entity) {
            return false;
        }

        if (!$this->isValid($data)) {
            return false;
        }
        $data = $this->inputFilter->getValues();

        $this->entity->populate($data);

        if ($this->eventManager) {
            $name = 'update:pre';
            $event = new Event($name, $this, array('entity' => $this->entity));
            $this->eventManager->trigger($event);

            if ($event->getParam('entity') && $event->getParam('entity') instanceof EntityInterface) {
                $this->entity = $event->getParam('entity');
            }
        }

        $this->repository->add($this->entity);

        $this->trigger('update:post', $this->entity);

        return $this->entity;
    }

    /**
     * @param string $event
     */
    protected function trigger($event, EntityInterface $entity)
    {
        if (!$this->eventManager) {
            return;
        }

        $this->eventManager->trigger($event, $this, compact('entity'));
    }

    public function activate($id)
    {
        /** @var $entity \T4webBase\Domain\Entity */
        $entity = $this->repository->find($this->criteriaFactory->getNativeCriteria('Id', $id));
        if (!$entity) {
            return false;
        }

        $entity->setActivated();

        $this->repository->add($entity);
        $this->trigger('activate:post', $entity);

        return true;
    }

    public function inactivate($id)
    {
        /** @var $entity \T4webBase\Domain\Entity */
        $entity = $this->repository->find($this->criteriaFactory->getNativeCriteria('Id', $id));
        if (!$entity) {
            return false;
        }

        $entity->setInactivated();

        $this->repository->add($entity);
        $this->trigger('inactivate:post', $entity);

        return true;
    }

    public function delete($id)
    {
        /** @var $entity \T4webBase\Domain\Entity */
        $entity = $this->repository->find($this->criteriaFactory->getNativeCriteria('Id', $id));
        if (!$entity) {
            return false;
        }

        $entity->setDeleted();

        $this->repository->add($entity);
        $this->trigger('delete:post', $entity);

        return true;
    }

    public function getValues()
    {
        return $this->inputFilter->getValues();
    }

    public function updateAll($attributeValue, $attributeName = 'id', array $data)
    {
        $criteria = $this->criteriaFactory->getNativeCriteria($attributeName, $attributeValue);
        $collection = $this->repository->findMany($criteria);
        if (!$collection->count()) {
            return false;
        }

        $this->repository->updateByAttribute($data, $attributeValue, $criteria->getField());

        return $collection;

    }
}
