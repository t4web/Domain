<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\Sql\Select;

class Criteria implements CriteriaInterface
{

    /**
     * @var string
     */
    private $entityName;

    /**
     * @var string
     */
    private $attribute;

    /**
     * @var string
     */
    private $predicate;

    /**
     * @var mixed
     */
    private $value;

    public function __construct($attribute, $predicate, $value, $entityName = null)
    {
        $this->attribute = $attribute;
        $this->predicate = $predicate;
        $this->value = $value;
        $this->entityName = $entityName;
    }

    /**
     * @param Select $select
     * @return viod
     */
    public function build(Select $select)
    {
        if (!method_exists('Zend\Db\Sql\Predicate\Predicate', $this->predicate)) {
            throw new \RuntimeException(sprintf('Bad predicate "%s"', $this->predicate));
        }

        $select->where->{$this->predicate}($this->attribute, $this->value);
    }
}