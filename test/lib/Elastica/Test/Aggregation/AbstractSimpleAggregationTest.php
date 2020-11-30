<?php
namespace Webonyx\Elastica3x\Test\Aggregation;

class AbstractSimpleAggregationTest extends BaseAggregationTest
{
    public function setUp()
    {
        $this->aggregation = $this->getMockForAbstractClass(
            'Webonyx\Elastica3x\Aggregation\AbstractSimpleAggregation',
            ['whatever']
        );
    }

    public function testToArrayThrowsExceptionOnUnsetParams()
    {
        $this->setExpectedException(
            'Webonyx\Elastica3x\Exception\InvalidException',
            'Either the field param or the script param should be set'
        );

        $this->aggregation->toArray();
    }
}
