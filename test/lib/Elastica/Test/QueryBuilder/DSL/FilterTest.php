<?php
namespace Webonyx\Elastica3x\Test\QueryBuilder\DSL;

use Webonyx\Elastica3x\Filter\Exists;
use Webonyx\Elastica3x\Query\Match;
use Webonyx\Elastica3x\QueryBuilder\DSL;

class FilterTest extends AbstractDSLTest
{
    /**
     * @group unit
     */
    public function testType()
    {
        $filterDSL = new DSL\Filter();

        $this->assertInstanceOf('Webonyx\Elastica3x\QueryBuilder\DSL', $filterDSL);
        $this->assertEquals(DSL::TYPE_FILTER, $filterDSL->getType());
    }

    /**
     * @group unit
     */
    public function testInterface()
    {
        $filterDSL = new DSL\Filter();

        $this->hideDeprecated();
        $this->_assertImplemented($filterDSL, 'bool', 'Webonyx\Elastica3x\Filter\BoolFilter', []);
        $this->_assertImplemented($filterDSL, 'bool_and', 'Webonyx\Elastica3x\Filter\BoolAnd', [[new Exists('field')]]);
        $this->_assertImplemented($filterDSL, 'bool_not', 'Webonyx\Elastica3x\Filter\BoolNot', [new Exists('field')]);
        $this->_assertImplemented($filterDSL, 'bool_or', 'Webonyx\Elastica3x\Filter\BoolOr', [[new Exists('field')]]);
        $this->_assertImplemented($filterDSL, 'exists', 'Webonyx\Elastica3x\Filter\Exists', ['field']);
        $this->_assertImplemented($filterDSL, 'geo_bounding_box', 'Webonyx\Elastica3x\Filter\GeoBoundingBox', ['field', [1, 2]]);
        $this->_assertImplemented($filterDSL, 'geo_distance', 'Webonyx\Elastica3x\Filter\GeoDistance', ['key', 'location', 'distance']);
        $this->_assertImplemented($filterDSL, 'geo_distance_range', 'Webonyx\Elastica3x\Filter\GeoDistanceRange', ['key', 'location']);
        $this->_assertImplemented($filterDSL, 'geo_polygon', 'Webonyx\Elastica3x\Filter\GeoPolygon', ['key', []]);
        $this->_assertImplemented($filterDSL, 'geo_shape_pre_indexed', 'Webonyx\Elastica3x\Filter\GeoShapePreIndexed', ['path', 'indexedId', 'indexedType', 'indexedIndex', 'indexedPath']);
        $this->_assertImplemented($filterDSL, 'geo_shape_provided', 'Webonyx\Elastica3x\Filter\GeoShapeProvided', ['path', []]);
        $this->_assertImplemented($filterDSL, 'geohash_cell', 'Webonyx\Elastica3x\Filter\GeohashCell', ['field', 'location']);
        $this->_assertImplemented($filterDSL, 'has_child', 'Webonyx\Elastica3x\Filter\HasChild', [new Match(), 'type']);
        $this->_assertImplemented($filterDSL, 'has_parent', 'Webonyx\Elastica3x\Filter\HasParent', [new Match(), 'type']);
        $this->_assertImplemented($filterDSL, 'ids', 'Webonyx\Elastica3x\Filter\Ids', ['type', []]);
        $this->_assertImplemented($filterDSL, 'indices', 'Webonyx\Elastica3x\Filter\Indices', [new Exists('field'), []]);
        $this->_assertImplemented($filterDSL, 'limit', 'Webonyx\Elastica3x\Filter\Limit', [1]);
        $this->_assertImplemented($filterDSL, 'match_all', 'Webonyx\Elastica3x\Filter\MatchAll', []);
        $this->_assertImplemented($filterDSL, 'missing', 'Webonyx\Elastica3x\Filter\Missing', ['field']);
        $this->_assertImplemented($filterDSL, 'nested', 'Webonyx\Elastica3x\Filter\Nested', []);
        $this->_assertImplemented($filterDSL, 'numeric_range', 'Webonyx\Elastica3x\Filter\NumericRange', []);
        $this->_assertImplemented($filterDSL, 'prefix', 'Webonyx\Elastica3x\Filter\Prefix', ['field', 'prefix']);
        $this->_assertImplemented($filterDSL, 'query', 'Webonyx\Elastica3x\Filter\Query', [new Match()]);
        $this->_assertImplemented($filterDSL, 'range', 'Webonyx\Elastica3x\Filter\Range', ['field', []]);
        $this->_assertImplemented($filterDSL, 'regexp', 'Webonyx\Elastica3x\Filter\Regexp', ['field', 'regex']);
        $this->_assertImplemented($filterDSL, 'script', 'Webonyx\Elastica3x\Filter\Script', ['script']);
        $this->_assertImplemented($filterDSL, 'term', 'Webonyx\Elastica3x\Filter\Term', []);
        $this->_assertImplemented($filterDSL, 'terms', 'Webonyx\Elastica3x\Filter\Terms', ['field', []]);
        $this->_assertImplemented($filterDSL, 'type', 'Webonyx\Elastica3x\Filter\Type', ['type']);
        $this->showDeprecated();
    }
}
