<?php
namespace Webonyx\Elastica3x\Test\Aggregation;

use Webonyx\Elastica3x\Aggregation\GeoCentroid;
use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Type\Mapping;

class GeoCentroidTest extends BaseAggregationTest
{
    protected function _getIndexForTest()
    {
        $index = $this->_createIndex();
        $type = $index->getType('test');

        $type->setMapping(new Mapping(null, [
            'location' => ['type' => 'geo_point'],
        ]));

        $type->addDocuments([
            new Document(1, ['location' => ['lat' => 32.849437, 'lon' => -117.271732]]),
            new Document(2, ['location' => ['lat' => 32.798320, 'lon' => -117.246648]]),
            new Document(3, ['location' => ['lat' => 37.782439, 'lon' => -122.392560]]),
        ]);

        $index->refresh();

        return $index;
    }

    /**
     * @group functional
     */
    public function testGeohashGridAggregation()
    {
        $agg = new GeoCentroid('centroid', 'location');

        $query = new Query();
        $query->addAggregation($agg);
        $results = $this->_getIndexForTest()->search($query)->getAggregation('centroid');

        $this->assertEquals(34.476731875911, $results['location']['lat']);
        $this->assertEquals(-118.97031342611, $results['location']['lon']);
    }
}
