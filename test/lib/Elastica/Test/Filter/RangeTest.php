<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\Range;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class RangeTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Range());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testAddField()
    {
        $rangeFilter = new Range();
        $returnValue = $rangeFilter->addField('fieldName', ['to' => 'value']);
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Range', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $field = 'field_name';
        $range = ['gte' => 10, 'lte' => 99];

        $filter = new Range();
        $filter->addField($field, $range);
        $expectedArray = ['range' => [$field => $range]];
        $this->assertEquals($expectedArray, $filter->toArray());
    }

    /**
     * @group unit
     */
    public function testSetExecution()
    {
        $field = 'field_name';
        $range = ['gte' => 10, 'lte' => 99];
        $filter = new Range('field_name', $range);

        $filter->setExecution('fielddata');
        $this->assertEquals('fielddata', $filter->getParam('execution'));

        $returnValue = $filter->setExecution('index');
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Range', $returnValue);
    }

    /**
     * Tests that parent fields are not overwritten by the toArray method.
     *
     * @group unit
     */
    public function testSetCachedNotOverwritten()
    {
        $filter = new Range('field_name', []);
        $filter->setCached(true);
        $array = $filter->toArray();
        $this->assertTrue($array['range']['_cache']);
    }
}
