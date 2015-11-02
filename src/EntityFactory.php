<?php

namespace T4webDomain;

use T4webDomainInterface\EntityFactoryInterface;
use T4webDomainInterface\EntityInterface;

class EntityFactory implements EntityFactoryInterface {

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
    public function __construct($entityClass, $collectionClass = 'ArrayObject') {
        $this->entityClass = $entityClass;
        $this->collectionClass = $collectionClass;
    }

    /**
     * @param array $data
     *
     * @return EntityInterface
     */
    public function create(array $data) {
        return new $this->entityClass($data);
    }

    /**
     * @param array $data
     *
     * @return ArrayObject
     */
    public function createCollection(array $data) {
        $collection = new $this->collectionClass();
        
        foreach ($data as $value) {
            $entity = $this->create($value);
            $collection->offsetSet($entity->getId(), $entity);
        }
        
        return $collection;
    }
    
}
