<?php

namespace T4webDomain\Infrastructure;

use T4webDomainInterface\Infrastructure\CriteriaInterface;
use T4webDomain\Infrastructure\Criteria;

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

}