<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Exists;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class ExistsTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $field = 'test';
        $query = new Exists($field);

        $expectedArray = ['exists' => ['field' => $field]];
        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group unit
     */
    public function testSetField()
    {
        $field = 'test';
        $query = new Exists($field);

        $this->assertEquals($field, $query->getParam('field'));

        $newField = 'hello world';
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Exists', $query->setField($newField));

        $this->assertEquals($newField, $query->getParam('field'));
    }
}
