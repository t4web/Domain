<?php

namespace T4webDomain;

use ArrayObject;

class Collection extends ArrayObject
{
    /**
     * @param string $entityMethod
     * @return static
     */
    public function rebuildByEntityMethod($entityMethod)
    {
        $newCollection = new static();

        foreach ($this->getArrayCopy() as $entity) {
            $newCollection->offsetSet($entity->$entityMethod(), $entity);
        }

        return $newCollection;
    }

    /**
     * @param string $entityMethod
     * @return array
     */
    public function getValuesByEntityMethod($entityMethod)
    {
        $values = [];

        foreach ($this->getArrayCopy() as $entity) {
            $values[] = $entity->$entityMethod();
        }

        return $values;
    }

    /**
     * @param string $entityMethod
     * @param mixed $value
     * @return static
     */
    public function filter($entityMethod, $value)
    {
        $newCollection = new static();

        foreach ($this->getArrayCopy() as $entity) {
            if ($entity->$entityMethod() == $value) {
                $newCollection->offsetSet($entity->getId(), $entity);
            }
        }

        return $newCollection;
    }

    /**
     * @return Collection
     */
    public function rebuildById()
    {
        return $this->rebuildByEntityMethod('getId');
    }

    /**
     * @return array
     */
    public function getIds()
    {
        $ids = [];

        foreach ($this as $entity) {
            $ids[] = $entity->getId();
        }

        return $ids;
    }

    /**
     * @param $attribute
     * @param bool $unique
     * @return array
     */
    public function getValueByAttribute($attribute, $unique = true)
    {
        $methodName = 'get' . ucfirst($attribute);

        if (!method_exists(reset($this), $methodName)) {
            throw new \RuntimeException("Entity " . get_class(reset($this)) . " does not have a method $methodName");
        }

        $result = [];

        foreach ($this as $entity) {
            $result[] = $entity->{$methodName}();
        }

        if ($unique) {
            $result = array_unique($result);
        }

        return $result;
    }

    /**
     * @return Entity
     */
    public function getFirst()
    {
        foreach ($this->getArrayCopy() as $entity) {
            return $entity;
        }
    }
}