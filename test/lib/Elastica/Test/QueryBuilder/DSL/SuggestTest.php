<?php
namespace Webonyx\Elastica3x\Test\QueryBuilder\DSL;

use Webonyx\Elastica3x\QueryBuilder\DSL;

class SuggestTest extends AbstractDSLTest
{
    /**
     * @group unit
     */
    public function testType()
    {
        $suggestDSL = new DSL\Suggest();

        $this->assertInstanceOf('Webonyx\Elastica3x\QueryBuilder\DSL', $suggestDSL);
        $this->assertEquals(DSL::TYPE_SUGGEST, $suggestDSL->getType());
    }

    /**
     * @group unit
     */
    public function testInterface()
    {
        $suggestDSL = new DSL\Suggest();

        $this->_assertImplemented($suggestDSL, 'completion', 'Webonyx\Elastica3x\Suggest\Completion', ['name', 'field']);
        $this->_assertImplemented($suggestDSL, 'phrase', 'Webonyx\Elastica3x\Suggest\Phrase', ['name', 'field']);
        $this->_assertImplemented($suggestDSL, 'term', 'Webonyx\Elastica3x\Suggest\Term', ['name', 'field']);

        $this->_assertNotImplemented($suggestDSL, 'context', []);
    }
}
