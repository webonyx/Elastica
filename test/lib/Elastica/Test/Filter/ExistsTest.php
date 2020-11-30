<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\Exists;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class ExistsTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Exists('test'));

        $this->assertFileDeprecated(
            $reflection->getFileName(),
            'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html'
        );
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $field = 'test';
        $filter = new Exists($field);

        $expectedArray = ['exists' => ['field' => $field]];
        $this->assertEquals($expectedArray, $filter->toArray());
    }

    /**
     * @group unit
     */
    public function testSetField()
    {
        $field = 'test';
        $filter = new Exists($field);

        $this->assertEquals($field, $filter->getParam('field'));

        $newField = 'hello world';
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Exists', $filter->setField($newField));

        $this->assertEquals($newField, $filter->getParam('field'));
    }
}
