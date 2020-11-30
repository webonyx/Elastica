<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Missing;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class MissingTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $query = new Missing('field_name');
        $expectedArray = ['missing' => ['field' => 'field_name']];
        $this->assertEquals($expectedArray, $query->toArray());

        $query = new Missing('field_name');
        $query->setExistence(true);
        $expectedArray = ['missing' => ['field' => 'field_name', 'existence' => true]];
        $this->assertEquals($expectedArray, $query->toArray());

        $query = new Missing('field_name');
        $query->setNullValue(true);
        $expectedArray = ['missing' => ['field' => 'field_name', 'null_value' => true]];
        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group unit
     */
    public function testSetField()
    {
        $query = new Missing('field_name');

        $this->assertEquals('field_name', $query->getParam('field'));

        $query->setField('new_field_name');
        $this->assertEquals('new_field_name', $query->getParam('field'));

        $returnValue = $query->setField('very_new_field_name');
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Missing', $returnValue);
    }

    /**
     * @group unit
     */
    public function testSetExistence()
    {
        $query = new Missing('field_name');

        $query->setExistence(true);
        $this->assertTrue($query->getParam('existence'));

        $query->setExistence(false);
        $this->assertFalse($query->getParam('existence'));

        $returnValue = $query->setExistence(true);
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Missing', $returnValue);
    }

    /**
     * @group unit
     */
    public function testSetNullValue()
    {
        $query = new Missing('field_name');

        $query->setNullValue(true);
        $this->assertTrue($query->getParam('null_value'));

        $query->setNullValue(false);
        $this->assertFalse($query->getParam('null_value'));

        $returnValue = $query->setNullValue(true);
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Missing', $returnValue);
    }
}
