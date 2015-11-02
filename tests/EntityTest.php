<?php

namespace T4webDomainTest;

use T4webDomain\Entity;

class Task extends Entity
{
    protected $name;
    protected $assignee;
    protected $startDate;
}

class EntityTest extends \PHPUnit_Framework_TestCase
{
    private $data;
    private $task;

    public function setUp()
    {
        $this->data = [
            'id' => 11,
            'name' => 'Some name',
            'assignee' => 'MG',
            'foo' => 'bar',
        ];

        $this->task = new Task($this->data);
    }

    /**
     * @covers Task::populate
     */
    public function testPopulate()
    {
        $this->assertAttributeEquals($this->data['id'], 'id', $this->task);
        $this->assertAttributeEquals($this->data['name'], 'name', $this->task);
        $this->assertAttributeEquals($this->data['assignee'], 'assignee', $this->task);
        $this->assertAttributeEquals(null, 'startDate', $this->task);
    }

    public function testExtract()
    {
        $extractedData = $this->task->extract();

        $this->assertEquals($this->data['id'], $extractedData['id']);
        $this->assertEquals($this->data['name'], $extractedData['name']);
        $this->assertEquals($this->data['assignee'], $extractedData['assignee']);

        $extractedData = $this->task->extract(['id', 'name']);

        $this->assertEquals($this->data['id'], $extractedData['id']);
        $this->assertEquals($this->data['name'], $extractedData['name']);
        $this->assertArrayNotHasKey('assignee', $extractedData);
    }
}
