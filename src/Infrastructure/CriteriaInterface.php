<?php

namespace T4webDomain\Infrastructure;

use Zend\Db\Sql\Select;

interface CriteriaInterface
{
    /**
     * @param Select $select
     * @return viod
     */
    public function build(Select $select);
}