<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\Type;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class TypeTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Type());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testSetType()
    {
        $typeFilter = new Type();
        $returnValue = $typeFilter->setType('type_name');
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Type', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $typeFilter = new Type('type_name');

        $expectedArray = [
            'type' => ['value' => 'type_name'],
        ];

        $this->assertEquals($expectedArray, $typeFilter->toArray());
    }
}
