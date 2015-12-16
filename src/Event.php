<?php

namespace T4webDomain;

use T4webDomainInterface\EntityInterface;
use T4webDomainInterface\EventInterface;

class Event implements EventInterface {

    /**
     * @var string
     */
    protected $name;

    /**
     * @var EntityInterface
     */
    protected $entity;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * Event constructor.
     * @param string $name
     * @param EntityInterface $entity
     * @param array $data
     */
    public function __construct($name, EntityInterface $entity, array $data = []) {
        $this->name = $name;
        $this->entity = $entity;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return EntityInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
