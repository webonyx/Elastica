<?php
namespace Webonyx\Elastica3x\Test\QueryBuilder\DSL;

use Webonyx\Elastica3x\Filter\Exists;
use Webonyx\Elastica3x\Query\Term;
use Webonyx\Elastica3x\QueryBuilder\DSL;

class AggregationTest extends AbstractDSLTest
{
    /**
     * @group unit
     */
    public function testType()
    {
        $aggregationDSL = new DSL\Aggregation();

        $this->assertInstanceOf('Webonyx\Elastica3x\QueryBuilder\DSL', $aggregationDSL);
        $this->assertEquals(DSL::TYPE_AGGREGATION, $aggregationDSL->getType());
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testFilteredInvalid()
    {
        $queryDSL = new DSL\Aggregation();
        $queryDSL->filter(null, $this);
    }

    /**
     * @group unit
     */
    public function testInterface()
    {
        $aggregationDSL = new DSL\Aggregation();

        $this->_assertImplemented($aggregationDSL, 'avg', 'Webonyx\Elastica3x\Aggregation\Avg', ['name']);
        $this->_assertImplemented($aggregationDSL, 'cardinality', 'Webonyx\Elastica3x\Aggregation\Cardinality', ['name']);
        $this->_assertImplemented($aggregationDSL, 'date_histogram', 'Webonyx\Elastica3x\Aggregation\DateHistogram', ['name', 'field', 1]);
        $this->_assertImplemented($aggregationDSL, 'date_range', 'Webonyx\Elastica3x\Aggregation\DateRange', ['name']);
        $this->_assertImplemented($aggregationDSL, 'extended_stats', 'Webonyx\Elastica3x\Aggregation\ExtendedStats', ['name']);
        $this->hideDeprecated();
        $this->_assertImplemented($aggregationDSL, 'filter', 'Webonyx\Elastica3x\Aggregation\Filter', ['name', new Exists('field')]);
        $this->showDeprecated();

        $this->_assertImplemented($aggregationDSL, 'filter', 'Webonyx\Elastica3x\Aggregation\Filter', ['name', new Term()]);

        $this->_assertImplemented($aggregationDSL, 'filters', 'Webonyx\Elastica3x\Aggregation\Filters', ['name']);
        $this->_assertImplemented($aggregationDSL, 'geo_distance', 'Webonyx\Elastica3x\Aggregation\GeoDistance', ['name', 'field', 'origin']);
        $this->_assertImplemented($aggregationDSL, 'geohash_grid', 'Webonyx\Elastica3x\Aggregation\GeohashGrid', ['name', 'field']);
        $this->_assertImplemented($aggregationDSL, 'global_agg', 'Webonyx\Elastica3x\Aggregation\GlobalAggregation', ['name']);
        $this->_assertImplemented($aggregationDSL, 'histogram', 'Webonyx\Elastica3x\Aggregation\Histogram', ['name', 'field', 1]);
        $this->_assertImplemented($aggregationDSL, 'ipv4_range', 'Webonyx\Elastica3x\Aggregation\IpRange', ['name', 'field']);
        $this->_assertImplemented($aggregationDSL, 'max', 'Webonyx\Elastica3x\Aggregation\Max', ['name']);
        $this->_assertImplemented($aggregationDSL, 'min', 'Webonyx\Elastica3x\Aggregation\Min', ['name']);
        $this->_assertImplemented($aggregationDSL, 'missing', 'Webonyx\Elastica3x\Aggregation\Missing', ['name', 'field']);
        $this->_assertImplemented($aggregationDSL, 'nested', 'Webonyx\Elastica3x\Aggregation\Nested', ['name', 'path']);
        $this->_assertImplemented($aggregationDSL, 'percentiles', 'Webonyx\Elastica3x\Aggregation\Percentiles', ['name']);
        $this->_assertImplemented($aggregationDSL, 'range', 'Webonyx\Elastica3x\Aggregation\Range', ['name']);
        $this->_assertImplemented($aggregationDSL, 'reverse_nested', 'Webonyx\Elastica3x\Aggregation\ReverseNested', ['name']);
        $this->_assertImplemented($aggregationDSL, 'scripted_metric', 'Webonyx\Elastica3x\Aggregation\ScriptedMetric', ['name']);
        $this->_assertImplemented($aggregationDSL, 'significant_terms', 'Webonyx\Elastica3x\Aggregation\SignificantTerms', ['name']);
        $this->_assertImplemented($aggregationDSL, 'stats', 'Webonyx\Elastica3x\Aggregation\Stats', ['name']);
        $this->_assertImplemented($aggregationDSL, 'sum', 'Webonyx\Elastica3x\Aggregation\Sum', ['name']);
        $this->_assertImplemented($aggregationDSL, 'terms', 'Webonyx\Elastica3x\Aggregation\Terms', ['name']);
        $this->_assertImplemented($aggregationDSL, 'top_hits', 'Webonyx\Elastica3x\Aggregation\TopHits', ['name']);
        $this->_assertImplemented($aggregationDSL, 'value_count', 'Webonyx\Elastica3x\Aggregation\ValueCount', ['name', 'field']);
        $this->_assertImplemented($aggregationDSL, 'bucket_script', 'Webonyx\Elastica3x\Aggregation\BucketScript', ['name']);
        $this->_assertImplemented($aggregationDSL, 'serial_diff', 'Webonyx\Elastica3x\Aggregation\SerialDiff', ['name']);

        $this->_assertNotImplemented($aggregationDSL, 'children', ['name']);
        $this->_assertNotImplemented($aggregationDSL, 'geo_bounds', ['name']);
        $this->_assertNotImplemented($aggregationDSL, 'percentile_ranks', ['name']);
    }
}
