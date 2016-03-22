<?php

namespace T4webDomain;

use T4webDomainInterface\EntityFactoryInterface;
use T4webDomainInterface\EntityInterface;

class EntityFactory implements EntityFactoryInterface
{

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var string
     */
    protected $collectionClass;

    /**
     * @param string $entityClass
     * @param string $collectionClass
     */
    public function __construct($entityClass, $collectionClass = 'ArrayObject')
    {
        $this->entityClass = $entityClass;
        $this->collectionClass = $collectionClass;
    }

    /**
     * @param array $data
     *
     * @return EntityInterface
     */
    public function create(array $data)
    {
        if (!isset($data['data']) && !isset($data['aggregateItems'])) {
            return new $this->entityClass($data);
        }

        $reflector = new \ReflectionClass($this->entityClass);

        $istanceArgs = [$data['data']];

        foreach ($data['aggregateItems'] as $aggregateItem) {
            $istanceArgs[] = $aggregateItem;
        }

        return $reflector->newInstanceArgs($istanceArgs);
    }

    /**
     * @param array $data
     *
     * @return ArrayObject
     */
    public function createCollection(array $data)
    {
        $collection = new $this->collectionClass();

        foreach ($data as $value) {
            $entity = $this->create($value);
            $collection->offsetSet($entity->getId(), $entity);
        }

        return $collection;
    }
}
