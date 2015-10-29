<?php

namespace T4webDomainTest;

use T4webDomain\Criteria;

class CriteriaTest extends \PHPUnit_Framework_TestCase
{

    public function testToArray()
    {
        $id = 2;

        $criteria = new Criteria('users');
        $criteria->equalTo('id', $id);
        $criteria->limit(4);
        $criteria->order('id');
        $criteria->in('type', [1,2,3]);

        $criteria->orCriteria()->limit('category', 4);

        $category = new Criteria('users');
        $category->equalTo('category', 4);
        $criteria->orCriteria($category);

        $photo = new Criteria('photos');
        $photo->in('status', [1,2,3]);
        $photo->limit(40);

        $photo = $criteria->relation('photos');

        $criteria->toArray();

        $schema = $criteria->toArray();

        $this->assertArrayHasKey('entityName', $schema);
        $this->assertEquals('users', $schema['entityName']);
        $this->assertArrayHasKey('predicate', $schema);
        $this->assertArrayHasKey('limit', $schema);
        $this->assertEquals(4, $schema['limit']);
        $this->assertArrayHasKey('order', $schema);
        $this->assertEquals('id', $schema['order']);
        $this->assertArrayHasKey('relations', $schema);
        $this->assertArrayHasKey('orCriteria', $schema);
    }
}
