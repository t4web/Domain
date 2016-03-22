<?php

namespace T4webDomainTest\Assets;

use T4webDomain\Entity;

class ComplexTask extends Entity
{
    protected $name;
    protected $projectId;
    protected $assigneeId;
    protected $startDate;

    protected $assignee;
    protected $project;

    public function __construct(array $data, $assignee, $project)
    {
        parent::__construct($data);
        $this->assignee = $assignee;
        $this->project = $project;
    }

    /**
     * @return mixed
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }
}
