<?php
namespace Webonyx\Elastica3x\Test\Aggregation;

use Webonyx\Elastica3x\Aggregation\Min;
use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;

class MinTest extends BaseAggregationTest
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
     * @group functional
     */
    public function testMinAggregation()
    {
        $agg = new Min('min_price');
        $agg->setField('price');

        $query = new Query();
        $query->addAggregation($agg);
        $results = $this->_getIndexForTest()->search($query)->getAggregation('min_price');

        $this->assertEquals(1, $results['value']);
    }
}
