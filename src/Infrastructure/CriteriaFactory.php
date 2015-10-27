<?php

namespace T4webDomain\Infrastructure;

use T4webDomainInterface\Infrastructure\CriteriaInterface;

class CriteriaFactory
{
    /**
     * @param array $filter
     * @return CriteriaInterface[]
     */
    public function build(array $filter = [])
    {
        return [new Criteria()];
    }

    /**
     * @param int $id
     * @return CriteriaInterface[]
     */
    public function buildIdCriteria($id)
    {
        return [new Criteria()];
    }
}