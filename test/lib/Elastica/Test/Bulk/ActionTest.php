<?php
namespace Webonyx\Elastica3x\Test\Bulk;

use Webonyx\Elastica3x\Bulk\Action;
use Webonyx\Elastica3x\Index;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Type;

class ActionTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testAction()
    {
        $action = new Action();
        $this->assertEquals('index', $action->getOpType());
        $this->assertFalse($action->hasSource());

        $expected = '{"index":{}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setIndex('index');

        $expected = '{"index":{"_index":"index"}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setType('type');

        $expected = '{"index":{"_index":"index","_type":"type"}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setId(1);
        $expected = '{"index":{"_index":"index","_type":"type","_id":1}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setRouting(1);
        $expected = '{"index":{"_index":"index","_type":"type","_id":1,"_routing":1}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $client = $this->_getClient();
        $index = new Index($client, 'index2');
        $type = new Type($index, 'type2');

        $action->setIndex($index);

        $expected = '{"index":{"_index":"index2","_type":"type","_id":1,"_routing":1}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setType($type);

        $expected = '{"index":{"_index":"index2","_type":"type2","_id":1,"_routing":1}}'."\n";
        $this->assertEquals($expected, $action->toString());

        $action->setSource(['user' => 'name']);

        $expected = '{"index":{"_index":"index2","_type":"type2","_id":1,"_routing":1}}'."\n";
        $expected .= '{"user":"name"}'."\n";

        $this->assertEquals($expected, $action->toString());
        $this->assertTrue($action->hasSource());

        $this->assertFalse(Action::isValidOpType('foo'));
        $this->assertTrue(Action::isValidOpType('delete'));
    }
}
