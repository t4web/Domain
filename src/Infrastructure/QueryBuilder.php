<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\Sql\Select;

class QueryBuilder
{

    /**
     * @var string
     */
    protected $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param CriteriaInterface $criteria
     * @return Select
     */
    public function getSelect(CriteriaInterface $criteria)
    {
        $select = new Select();
        $select->from($this->tableName);

        $criteria->build($select);

        return $select;
    }

}