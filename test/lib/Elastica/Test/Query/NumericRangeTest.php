<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\NumericRange;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class NumericRangeTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testAddField()
    {
        $rangeQuery = new NumericRange();
        $returnValue = $rangeQuery->addField('fieldName', ['to' => 'value']);
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\NumericRange', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $query = new NumericRange();

        $fromTo = ['from' => 'ra', 'to' => 'ru'];
        $query->addField('name', $fromTo);

        $expectedArray = [
            'numeric_range' => [
                'name' => $fromTo,
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }
}
