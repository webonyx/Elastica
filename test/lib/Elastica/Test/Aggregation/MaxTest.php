<?php
namespace Webonyx\Elastica3x\Test\Aggregation;

use Webonyx\Elastica3x\Aggregation\Max;
use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Script\Script;

class MaxTest extends BaseAggregationTest
{
    protected function _getIndexForTest()
    {
        $index = $this->_createIndex();

        $index->getType('test')->addDocuments([
            new Document(1, ['price' => 5]),
            new Document(2, ['price' => 8]),
            new Document(3, ['price' => 1]),
            new Document(4, ['price' => 3]),
        ]);

        $index->refresh();

        return $index;
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $expected = [
            'max' => [
                'field' => 'price',
                'script' => '_value * conversion_rate',
                'params' => [
                    'conversion_rate' => 1.2,
                ],
            ],
            'aggs' => [
                'subagg' => ['max' => ['field' => 'foo']],
            ],
        ];

        $agg = new Max('min_price_in_euros');
        $agg->setField('price');
        $agg->setScript(new Script('_value * conversion_rate', ['conversion_rate' => 1.2]));
        $max = new Max('subagg');
        $max->setField('foo');
        $agg->addAggregation($max);

        $this->assertEquals($expected, $agg->toArray());
    }

    /**
     * @group functional
     */
    public function testMaxAggregation()
    {
        $this->_checkScriptInlineSetting();
        $index = $this->_getIndexForTest();

        $agg = new Max('min_price');
        $agg->setField('price');

        $query = new Query();
        $query->addAggregation($agg);
        $results = $index->search($query)->getAggregation('min_price');

        $this->assertEquals(8, $results['value']);

        // test using a script
        $agg->setScript(new Script('_value * conversion_rate', ['conversion_rate' => 1.2]));
        $query = new Query();
        $query->addAggregation($agg);
        $results = $index->search($query)->getAggregation('min_price');

        $this->assertEquals(8 * 1.2, $results['value']);
    }
}
