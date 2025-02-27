<?php
namespace Webonyx\Elastica3x\Test\Aggregation;

use Webonyx\Elastica3x\Aggregation\Sum;
use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Script\Script;

class ScriptTest extends BaseAggregationTest
{
    protected function _getIndexForTest()
    {
        $index = $this->_createIndex();

        $index->getType('test')->addDocuments([
            new Document('1', ['price' => 5]),
            new Document('2', ['price' => 8]),
            new Document('3', ['price' => 1]),
            new Document('4', ['price' => 3]),
        ]);

        $index->refresh();

        return $index;
    }

    /**
     * @group functional
     */
    public function testAggregationScript()
    {
        $this->_checkScriptInlineSetting();
        $agg = new Sum('sum');
        // x = (0..1) is groovy-specific syntax, to see if lang is recognized
        $script = new Script("x = (0..1); return doc['price'].value", null, 'groovy');
        $agg->setScript($script);

        $query = new Query();
        $query->addAggregation($agg);
        $results = $this->_getIndexForTest()->search($query)->getAggregation('sum');

        $this->assertEquals(5 + 8 + 1 + 3, $results['value']);
    }

    /**
     * @group functional
     */
    public function testAggregationScriptAsString()
    {
        $this->_checkScriptInlineSetting();
        $agg = new Sum('sum');
        $agg->setScript("doc['price'].value");

        $query = new Query();
        $query->addAggregation($agg);
        $results = $this->_getIndexForTest()->search($query)->getAggregation('sum');

        $this->assertEquals(5 + 8 + 1 + 3, $results['value']);
    }

    /**
     * @group unit
     */
    public function testSetScript()
    {
        $aggregation = 'sum';
        $string = "doc['price'].value";
        $params = [
            'param1' => 'one',
            'param2' => 1,
        ];
        $lang = 'groovy';

        $agg = new Sum($aggregation);
        $script = new Script($string, $params, $lang);
        $agg->setScript($script);

        $array = $agg->toArray();

        $expected = [
            $aggregation => [
                'script' => $string,
                'params' => $params,
                'lang' => $lang,
            ],
        ];
        $this->assertEquals($expected, $array);
    }
}
