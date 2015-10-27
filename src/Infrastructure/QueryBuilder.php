<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\Sql\Select;

class QueryBuilder
{

    /**
     * @param CriteriaInterface $criteria
     * @return Select
     */
    public function getSelect(CriteriaInterface $criteria)
    {
        $select = new Select();

        $criteria->build($select);

        return $select;
    }

}