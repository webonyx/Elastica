<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\NumericRange;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class NumericRangeTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new NumericRange());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testAddField()
    {
        $rangeFilter = new NumericRange();
        $returnValue = $rangeFilter->addField('fieldName', ['to' => 'value']);
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\NumericRange', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $filter = new NumericRange();

        $fromTo = ['from' => 'ra', 'to' => 'ru'];
        $filter->addField('name', $fromTo);

        $expectedArray = [
            'numeric_range' => [
                'name' => $fromTo,
            ],
        ];

        $this->assertEquals($expectedArray, $filter->toArray());
    }
}
