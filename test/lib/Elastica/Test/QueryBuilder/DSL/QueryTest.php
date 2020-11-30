<?php
namespace Webonyx\Elastica3x\Test\QueryBuilder\DSL;

use Webonyx\Elastica3x\Filter\Exists;
use Webonyx\Elastica3x\Query\Match;
use Webonyx\Elastica3x\Query\Term;
use Webonyx\Elastica3x\QueryBuilder\DSL;

class QueryTest extends AbstractDSLTest
{
    /**
     * @group unit
     */
    public function testType()
    {
        $queryDSL = new DSL\Query();

        $this->assertInstanceOf('Webonyx\Elastica3x\QueryBuilder\DSL', $queryDSL);
        $this->assertEquals(DSL::TYPE_QUERY, $queryDSL->getType());
    }

    /**
     * @group unit
     */
    public function testMatch()
    {
        $queryDSL = new DSL\Query();

        $match = $queryDSL->match('field', 'match');
        $this->assertEquals('match', $match->getParam('field'));
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Match', $match);
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testConstantScoreFilterInvalid()
    {
        $queryDSL = new DSL\Query();
        $queryDSL->constant_score($this);
    }

    /**
     * @group unit
     */
    public function testConstantScoreWithLegacyFilterDeprecated()
    {
        $this->hideDeprecated();
        $existsFilter = new Exists('test');
        $this->showDeprecated();

        $queryDSL = new DSL\Query();

        $errorsCollector = $this->startCollectErrors();
        $queryDSL->constant_score($existsFilter);
        $this->finishCollectErrors();

        $errorsCollector->assertOnlyDeprecatedErrors(
            [
                'Deprecated: Webonyx\Elastica3x\Query\ConstantScore passing AbstractFilter is deprecated. Pass AbstractQuery instead.',
                'Deprecated: Webonyx\Elastica3x\Query\ConstantScore::setFilter passing AbstractFilter is deprecated. Pass AbstractQuery instead.',
            ]
        );
    }

    /**
     * @group unit
     */
    public function testFilteredDeprecated()
    {
        $errorsCollector = $this->startCollectErrors();

        $queryDSL = new DSL\Query();
        $queryDSL->filtered(null, new Exists('term'));
        $this->finishCollectErrors();

        $errorsCollector->assertOnlyDeprecatedErrors(
            [
                'Use bool() instead. Filtered query is deprecated since ES 2.0.0-beta1 and this method will be removed in further Webonyx\Elastica3x releases.',
                'Deprecated: Webonyx\Elastica3x\Query\Filtered passing AbstractFilter is deprecated. Pass AbstractQuery instead.',
                'Deprecated: Webonyx\Elastica3x\Query\Filtered::setFilter passing AbstractFilter is deprecated. Pass AbstractQuery instead.',
            ]
        );
    }

    /**
     * @group unit
     */
    public function testInterface()
    {
        $queryDSL = new DSL\Query();

        $this->_assertImplemented($queryDSL, 'bool', 'Webonyx\Elastica3x\Query\BoolQuery', []);
        $this->_assertImplemented($queryDSL, 'boosting', 'Webonyx\Elastica3x\Query\Boosting', []);
        $this->_assertImplemented($queryDSL, 'common_terms', 'Webonyx\Elastica3x\Query\Common', ['field', 'query', 0.001]);
        $this->_assertImplemented($queryDSL, 'constant_score', 'Webonyx\Elastica3x\Query\ConstantScore', [new Match()]);
        $this->_assertImplemented($queryDSL, 'dis_max', 'Webonyx\Elastica3x\Query\DisMax', []);

        $this->hideDeprecated();
        $this->_assertImplemented($queryDSL, 'filtered', 'Webonyx\Elastica3x\Query\Filtered', [new Match(), new Exists('field')]);
        $this->_assertImplemented($queryDSL, 'filtered', 'Webonyx\Elastica3x\Query\Filtered', [new Match(), new Term()]);
        $this->showDeprecated();

        $this->_assertImplemented($queryDSL, 'function_score', 'Webonyx\Elastica3x\Query\FunctionScore', []);
        $this->_assertImplemented($queryDSL, 'fuzzy', 'Webonyx\Elastica3x\Query\Fuzzy', ['field', 'type']);
        $this->_assertImplemented($queryDSL, 'has_child', 'Webonyx\Elastica3x\Query\HasChild', [new Match()]);
        $this->_assertImplemented($queryDSL, 'has_parent', 'Webonyx\Elastica3x\Query\HasParent', [new Match(), 'type']);
        $this->_assertImplemented($queryDSL, 'ids', 'Webonyx\Elastica3x\Query\Ids', ['type', []]);
        $this->_assertImplemented($queryDSL, 'match', 'Webonyx\Elastica3x\Query\Match', ['field', 'values']);
        $this->_assertImplemented($queryDSL, 'match_all', 'Webonyx\Elastica3x\Query\MatchAll', []);
        $this->_assertImplemented($queryDSL, 'more_like_this', 'Webonyx\Elastica3x\Query\MoreLikeThis', []);
        $this->_assertImplemented($queryDSL, 'multi_match', 'Webonyx\Elastica3x\Query\MultiMatch', []);
        $this->_assertImplemented($queryDSL, 'nested', 'Webonyx\Elastica3x\Query\Nested', []);
        $this->_assertImplemented($queryDSL, 'prefix', 'Webonyx\Elastica3x\Query\Prefix', []);
        $this->_assertImplemented($queryDSL, 'query_string', 'Webonyx\Elastica3x\Query\QueryString', []);
        $this->_assertImplemented($queryDSL, 'range', 'Webonyx\Elastica3x\Query\Range', ['field', []]);
        $this->_assertImplemented($queryDSL, 'regexp', 'Webonyx\Elastica3x\Query\Regexp', ['field', 'value', 1.0]);
        $this->_assertImplemented($queryDSL, 'simple_query_string', 'Webonyx\Elastica3x\Query\SimpleQueryString', ['query']);
        $this->_assertImplemented($queryDSL, 'term', 'Webonyx\Elastica3x\Query\Term', []);
        $this->_assertImplemented($queryDSL, 'terms', 'Webonyx\Elastica3x\Query\Terms', ['field', []]);
        $this->_assertImplemented($queryDSL, 'top_children', 'Webonyx\Elastica3x\Query\TopChildren', [new Match(), 'type']);
        $this->_assertImplemented($queryDSL, 'wildcard', 'Webonyx\Elastica3x\Query\Wildcard', []);
        $this->_assertImplemented(
            $queryDSL,
            'geo_distance',
            'Webonyx\Elastica3x\Query\GeoDistance',
            ['key', ['lat' => 1, 'lon' => 0], 'distance']
        );

        $this->_assertNotImplemented($queryDSL, 'custom_boost_factor', []);
        $this->_assertNotImplemented($queryDSL, 'custom_filters_score', []);
        $this->_assertNotImplemented($queryDSL, 'custom_score', []);
        $this->_assertNotImplemented($queryDSL, 'field', []);
        $this->_assertNotImplemented($queryDSL, 'geo_shape', []);
        $this->_assertNotImplemented($queryDSL, 'indices', []);
        $this->_assertNotImplemented($queryDSL, 'minimum_should_match', []);
        $this->_assertNotImplemented($queryDSL, 'more_like_this_field', []);
        $this->_assertNotImplemented($queryDSL, 'span_first', []);
        $this->_assertNotImplemented($queryDSL, 'span_multi_term', []);
        $this->_assertNotImplemented($queryDSL, 'span_near', []);
        $this->_assertNotImplemented($queryDSL, 'span_not', []);
        $this->_assertNotImplemented($queryDSL, 'span_or', []);
        $this->_assertNotImplemented($queryDSL, 'span_term', []);
        $this->_assertNotImplemented($queryDSL, 'template', []);
        $this->_assertNotImplemented($queryDSL, 'text', []);
    }
}
