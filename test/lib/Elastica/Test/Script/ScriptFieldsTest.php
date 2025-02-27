<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Script\Script;
use Webonyx\Elastica3x\Script\ScriptFields;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class ScriptFieldsTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testNewScriptFields()
    {
        $script = new Script('1 + 2');

        // addScript
        $scriptFields = new ScriptFields();
        $scriptFields->addScript('test', $script);
        $this->assertSame($scriptFields->getParam('test'), $script);

        // setScripts
        $scriptFields = new ScriptFields();
        $scriptFields->setScripts([
            'test' => $script,
        ]);
        $this->assertSame($scriptFields->getParam('test'), $script);

        // Constructor
        $scriptFields = new ScriptFields([
            'test' => $script,
        ]);
        $this->assertSame($scriptFields->getParam('test'), $script);
    }

    /**
     * @group unit
     */
    public function testSetScriptFields()
    {
        $query = new Query();
        $script = new Script('1 + 2');

        $scriptFields = new ScriptFields([
            'test' => $script,
        ]);
        $query->setScriptFields($scriptFields);
        $this->assertSame($query->getParam('script_fields'), $scriptFields);

        $query->setScriptFields([
            'test' => $script,
        ]);
        $this->assertSame($query->getParam('script_fields')->getParam('test'), $script);
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testNameException()
    {
        $script = new Script('1 + 2');
        $scriptFields = new ScriptFields([$script]);
    }

    /**
     * @group functional
     */
    public function testQuery()
    {
        $this->_checkScriptInlineSetting();

        $index = $this->_createIndex();

        $type = $index->getType('test');

        $doc = new Document(1, ['firstname' => 'guschti', 'lastname' => 'ruflin']);
        $type->addDocument($doc);
        $index->refresh();

        $query = new Query();
        $script = new Script('1 + 2');
        $scriptFields = new ScriptFields([
            'test' => $script,
        ]);
        $query->setScriptFields($scriptFields);

        $resultSet = $type->search($query);
        $first = $resultSet->current()->getData();

        // 1 + 2
        $this->assertEquals(3, $first['test'][0]);
    }
}
