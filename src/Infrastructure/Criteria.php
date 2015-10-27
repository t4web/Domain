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

    }
}