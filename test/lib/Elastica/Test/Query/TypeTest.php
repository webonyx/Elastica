<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Type;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class TypeTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testSetType()
    {
        $typeQuery = new Type();
        $returnValue = $typeQuery->setType('type_name');
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Type', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $typeQuery = new Type('type_name');

        $expectedArray = [
            'type' => ['value' => 'type_name'],
        ];

        $this->assertEquals($expectedArray, $typeQuery->toArray());
    }
}
