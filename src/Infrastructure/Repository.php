<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use T4webDomainInterface\Infrastructure\RepositoryInterface;
use T4webDomainInterface\EntityInterface;

class Repository implements RepositoryInterface
{
    /**
     * @var TableGateway
     */
    protected $tableGateway;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var IdentityMap
     */
    protected $identityMap;

    /**
     * @var IdentityMap
     */
    protected $identityMapOriginal;

    public function __construct(
        TableGateway $tableGateway,
        Mapper $mapper,
        QueryBuilder $queryBuilder,
        IdentityMap $identityMap,
        IdentityMap $identityMapOriginal
    )
    {
        $this->tableGateway = $tableGateway;
        $this->mapper = $mapper;
        $this->queryBuilder = $queryBuilder;
        $this->identityMap = $identityMap;
        $this->identityMapOriginal = $identityMapOriginal;
    }

    /**
     * @param EntityInterface $entity
     * @return EntityInterface
     */
    public function add(EntityInterface $entity)
    {
        $id = $entity->getId();

        if ($this->identityMap->offsetExists((int)$id)) {
            if (!$this->isEntityChanged($entity)) {
                return;
            }

            //$e = $this->getEvent();
            $originalEntity = $this->identityMapOriginal->offsetGet($entity->getId());
            //$e->setOriginalEntity($originalEntity);
            //$e->setChangedEntity($entity);

            //$this->triggerPreChanges($e);

            $result = $this->tableGateway->update($this->mapper->toTableRow($entity), ['id' => $id]);

            //$this->triggerChanges($e);
            //$this->triggerAttributesChange($e);

            return $result;
        } else {
            $this->tableGateway->insert($this->mapper->toTableRow($entity));

            if (empty($id)) {
                $id = $this->tableGateway->getLastInsertValue();
                $entity->populate(compact('id'));
            }

            $this->toIdentityMap($entity);

            //$this->triggerCreate($entity);
        }

        return $entity;
    }

    /**
     * @param EntityInterface $entity
     * @return void
     */
    public function remove(EntityInterface $entity)
    {
        $id = $entity->getId();

        if (empty($id)) {
            return;
        }

        return $this->tableGateway->delete(['id' => $id]);
    }

    /**
     * @param mixed $criteria
     * @return EntityInterface|null
     */
    public function find($criteria)
    {
        $select = $this->queryBuilder->getSelect($criteria);

        $select->limit(1)->offset(0);
        $result = $this->tableGateway->selectWith($select)->toArray();

        if (!isset($result[0])) {
            return;
        }

        $entity = $this->mapper->fromTableRow($result[0]);

        $this->toIdentityMap($entity);

        return $entity;
    }

    /**
     * @param mixed $criteria
     * @return EntityInterface[]
     */
    public function findMany($criteria)
    {
        $select = $this->queryBuilder->getSelect($criteria);

        $rows = $this->tableGateway->selectWith($select)->toArray();

        $entities = $this->mapper->fromTableRows($rows);

        foreach ($entities as $entity) {
            $this->toIdentityMap($entity);
        }

        return $entities;
    }

    /**
     * @param mixed $criteria
     * @return int
     */
    public function count($criteria)
    {
        $select = $this->queryBuilder->getSelect($criteria);
        $select->columns(["row_count" => new Expression("COUNT(*)")]);

        $result = $this->tableGateway->selectWith($select)->toArray();

        if (!isset($result[0])) {
            return 0;
        }

        return $result[0]['row_count'];
    }

    /**
     * @param EntityInterface $entity
     */
    protected function toIdentityMap(EntityInterface $entity)
    {
        $this->identityMap->offsetSet($entity->getId(), $entity);
        $this->identityMapOriginal->offsetSet($entity->getId(), clone $entity);
    }

    /**
     * @param EntityInterface $changedEntity
     * @return bool
     */
    protected function isEntityChanged(EntityInterface $changedEntity)
    {
        $originalEntity = $this->identityMapOriginal->offsetGet($changedEntity->getId());
        return $changedEntity != $originalEntity;
    }
}