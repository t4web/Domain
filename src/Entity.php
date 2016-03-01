<?php

namespace T4webDomain;

use T4webDomainInterface\EntityInterface;

class Entity implements EntityInterface
{

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->populate($data);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $properties
     * @return array
     */
    public function extract(array $properties = [])
    {
        $state = get_object_vars($this);

        if (empty($properties)) {
            return $state;
        }

        $rawArray = array_fill_keys($properties, null);

        return array_intersect_key($state, $rawArray);
    }

    /**
     * @param array $array
     * @return $this
     */
    public function populate(array $array = [])
    {
        $state = get_object_vars($this);

        $stateIntersect = array_intersect_key($array, $state);

        foreach ($stateIntersect as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }
}
