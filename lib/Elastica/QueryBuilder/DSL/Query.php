<?php
namespace Webonyx\Elastica3x\QueryBuilder\DSL;

use Webonyx\Elastica3x\Exception\DeprecatedException;
use Webonyx\Elastica3x\Exception\NotImplementedException;
use Webonyx\Elastica3x\Filter\AbstractFilter;
use Webonyx\Elastica3x\Query\AbstractQuery;
use Webonyx\Elastica3x\Query\BoolQuery;
use Webonyx\Elastica3x\Query\Boosting;
use Webonyx\Elastica3x\Query\Common;
use Webonyx\Elastica3x\Query\ConstantScore;
use Webonyx\Elastica3x\Query\DisMax;
use Webonyx\Elastica3x\Query\Filtered;
use Webonyx\Elastica3x\Query\FunctionScore;
use Webonyx\Elastica3x\Query\Fuzzy;
use Webonyx\Elastica3x\Query\GeoDistance;
use Webonyx\Elastica3x\Query\HasChild;
use Webonyx\Elastica3x\Query\HasParent;
use Webonyx\Elastica3x\Query\Ids;
use Webonyx\Elastica3x\Query\Match;
use Webonyx\Elastica3x\Query\MatchAll;
use Webonyx\Elastica3x\Query\MoreLikeThis;
use Webonyx\Elastica3x\Query\MultiMatch;
use Webonyx\Elastica3x\Query\Nested;
use Webonyx\Elastica3x\Query\Prefix;
use Webonyx\Elastica3x\Query\QueryString;
use Webonyx\Elastica3x\Query\Range;
use Webonyx\Elastica3x\Query\Regexp;
use Webonyx\Elastica3x\Query\SimpleQueryString;
use Webonyx\Elastica3x\Query\Term;
use Webonyx\Elastica3x\Query\Terms;
use Webonyx\Elastica3x\Query\TopChildren;
use Webonyx\Elastica3x\Query\Wildcard;
use Webonyx\Elastica3x\QueryBuilder\DSL;

/**
 * elasticsearch query DSL.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-queries.html
 */
class Query implements DSL
{
    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType()
    {
        return self::TYPE_QUERY;
    }

    /**
     * match query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-query.html
     *
     * @param string $field
     * @param mixed  $values
     *
     * @return Match
     */
    public function match($field = null, $values = null)
    {
        return new Match($field, $values);
    }

    /**
     * multi match query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-multi-match-query.html
     *
     * @return \Webonyx\Elastica3x\Query\MultiMatch
     */
    public function multi_match()
    {
        return new MultiMatch();
    }

    /**
     * bool query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
     *
     * @return \Webonyx\Elastica3x\Query\BoolQuery
     */
    public function bool()
    {
        return new BoolQuery();
    }

    /**
     * boosting query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-boosting-query.html
     *
     * @return Boosting
     */
    public function boosting()
    {
        return new Boosting();
    }

    /**
     * common terms query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-common-terms-query.html
     *
     * @param string $field
     * @param string $query
     * @param float  $cutoffFrequency percentage in decimal form (.001 == 0.1%)
     *
     * @return Common
     */
    public function common_terms($field, $query, $cutoffFrequency)
    {
        return new Common($field, $query, $cutoffFrequency);
    }

    /**
     * custom filters score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/0.90/query-dsl-custom-filters-score-query.html
     */
    public function custom_filters_score()
    {
        throw new NotImplementedException();
    }

    /**
     * custom score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/0.90/query-dsl-custom-score-query.html
     */
    public function custom_score()
    {
        throw new NotImplementedException();
    }

    /**
     * custom boost factor query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/0.90/query-dsl-custom-boost-factor-query.html
     */
    public function custom_boost_factor()
    {
        throw new NotImplementedException();
    }

    /**
     * constant score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html
     *
     * @param null|\Webonyx\Elastica3x\Filter\AbstractFilter|array $filter
     *
     * @return ConstantScore
     */
    public function constant_score($filter = null)
    {
        return new ConstantScore($filter);
    }

    /**
     * dis max query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-dis-max-query.html
     *
     * @return DisMax
     */
    public function dis_max()
    {
        return new DisMax();
    }

    /**
     * field query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/0.90/query-dsl-field-query.html
     */
    public function field()
    {
        throw new NotImplementedException();
    }

    /**
     * filtered query.
     *
     * @deprecated Use bool() instead. Filtered query is deprecated since ES 2.0.0-beta1 and this method will be removed in further Webonyx\Elastica3x releases.
     *
     * @param AbstractFilter $filter
     * @param AbstractQuery  $query
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-filtered-query.html
     *
     * @return Filtered
     */
    public function filtered(AbstractQuery $query = null, $filter = null)
    {
        trigger_error('Use bool() instead. Filtered query is deprecated since ES 2.0.0-beta1 and this method will be removed in further Webonyx\Elastica3x releases.', E_USER_DEPRECATED);

        return new Filtered($query, $filter);
    }

    /**
     * fuzzy like this query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-flt-query.html
     *
     * @return FuzzyLikeThis
     */
    public function fuzzy_like_this()
    {
        throw new NotImplementedException('Removed in elasticsearch 2.0');
    }

    /**
     * fuzzy like this field query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-flt-field-query.html
     */
    public function fuzzy_like_this_field()
    {
        throw new NotImplementedException('Removed in elasticsearch 2.0');
    }

    /**
     * function score query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-function-score-query.html
     *
     * @return FunctionScore
     */
    public function function_score()
    {
        return new FunctionScore();
    }

    /**
     * fuzzy query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-fuzzy-query.html
     *
     * @param string $fieldName Field name
     * @param string $value     String to search for
     *
     * @return Fuzzy
     */
    public function fuzzy($fieldName = null, $value = null)
    {
        return new Fuzzy($fieldName, $value);
    }

    /**
     * geo shape query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-shape-query.html
     */
    public function geo_shape()
    {
        throw new NotImplementedException();
    }

    /**
     * has child query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-query.html
     *
     * @param string|\Webonyx\Elastica3x\Query|\Webonyx\Elastica3x\Query\AbstractQuery $query
     * @param string                                               $type  Parent document type
     *
     * @return HasChild
     */
    public function has_child($query, $type = null)
    {
        return new HasChild($query, $type);
    }

    /**
     * has parent query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-parent-query.html
     *
     * @param string|\Webonyx\Elastica3x\Query|\Webonyx\Elastica3x\Query\AbstractQuery $query
     * @param string                                               $type  Parent document type
     *
     * @return HasParent
     */
    public function has_parent($query, $type)
    {
        return new HasParent($query, $type);
    }

    /**
     * ids query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-ids-query.html
     *
     * @param array|string|\Webonyx\Elastica3x\Type $type
     * @param array                       $ids
     *
     * @return Ids
     */
    public function ids($type = null, array $ids = [])
    {
        return new Ids($type, $ids);
    }

    /**
     * indices query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-indices-query.html
     */
    public function indices()
    {
        throw new NotImplementedException();
    }

    /**
     * match all query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-match-all-query.html
     *
     * @return MatchAll
     */
    public function match_all()
    {
        return new MatchAll();
    }

    /**
     * more like this query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-mlt-query.html
     *
     * @return MoreLikeThis
     */
    public function more_like_this()
    {
        return new MoreLikeThis();
    }

    /**
     * more_like_this_field query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/1.4/query-dsl-mlt-field-query.html
     * @deprecated More Like This Field query is deprecated as of ES 1.4 and will be removed in ES 2.0. Use MoreLikeThis query instead. This method will be removed in further Webonyx\Elastica3x releases
     */
    public function more_like_this_field()
    {
        throw new DeprecatedException('More Like This Field query is deprecated as of ES 1.4 and will be removed in ES 2.0. Use MoreLikeThis query instead. This method will be removed in further Webonyx\Elastica3x releases');
    }

    /**
     * nested query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-query.html
     *
     * @return Nested
     */
    public function nested()
    {
        return new Nested();
    }

    /**
     * prefix query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-prefix-query.html
     *
     * @param array $prefix Prefix array
     *
     * @return Prefix
     */
    public function prefix(array $prefix = [])
    {
        return new Prefix($prefix);
    }

    /**
     * query string query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-string-query.html
     *
     * @param string $queryString OPTIONAL Query string for object
     *
     * @return QueryString
     */
    public function query_string($queryString = '')
    {
        return new QueryString($queryString);
    }

    /**
     * simple_query_string query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-simple-query-string-query.html
     *
     * @param string $query
     * @param array  $fields
     *
     * @return SimpleQueryString
     */
    public function simple_query_string($query, array $fields = [])
    {
        return new SimpleQueryString($query, $fields);
    }

    /**
     * range query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-range-query.html
     *
     * @param string $fieldName
     * @param array  $args
     *
     * @return Range
     */
    public function range($fieldName = null, array $args = [])
    {
        return new Range($fieldName, $args);
    }

    /**
     * regexp query.
     *
     * @param string $key
     * @param string $value
     * @param float  $boost
     *
     * @return Regexp
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
     */
    public function regexp($key = '', $value = null, $boost = 1.0)
    {
        return new Regexp($key, $value, $boost);
    }

    /**
     * span first query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-first-query.html
     */
    public function span_first()
    {
        throw new NotImplementedException();
    }

    /**
     * span multi term query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-multi-term-query.html
     */
    public function span_multi_term()
    {
        throw new NotImplementedException();
    }

    /**
     * span near query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-near-query.html
     */
    public function span_near()
    {
        throw new NotImplementedException();
    }

    /**
     * span not query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-not-query.html
     */
    public function span_not()
    {
        throw new NotImplementedException();
    }

    /**
     * span or query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-or-query.html
     */
    public function span_or()
    {
        throw new NotImplementedException();
    }

    /**
     * span term query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-span-term-query.html
     */
    public function span_term()
    {
        throw new NotImplementedException();
    }

    /**
     * term query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-term-query.html
     *
     * @param array $term
     *
     * @return Term
     */
    public function term(array $term = [])
    {
        return new Term($term);
    }

    /**
     * terms query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-query.html
     *
     * @param string $key
     * @param array  $terms
     *
     * @return Terms
     */
    public function terms($key = '', array $terms = [])
    {
        return new Terms($key, $terms);
    }

    /**
     * top children query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-top-children-query.html
     *
     * @param string|AbstractQuery|\Webonyx\Elastica3x\Query $query
     * @param string                               $type
     *
     * @return TopChildren
     */
    public function top_children($query, $type = null)
    {
        return new TopChildren($query, $type);
    }

    /**
     * wildcard query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-wildcard-query.html
     *
     * @param string $key   OPTIONAL Wildcard key
     * @param string $value OPTIONAL Wildcard value
     * @param float  $boost OPTIONAL Boost value (default = 1)
     *
     * @return Wildcard
     */
    public function wildcard($key = '', $value = null, $boost = 1.0)
    {
        return new Wildcard($key, $value, $boost);
    }

    /**
     * text query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/0.90/query-dsl-text-query.html
     */
    public function text()
    {
        throw new NotImplementedException();
    }

    /**
     * minimum should match query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-minimum-should-match.html
     */
    public function minimum_should_match()
    {
        throw new NotImplementedException();
    }

    /**
     * template query.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-template-query.html
     */
    public function template()
    {
        throw new NotImplementedException();
    }

    /**
     * geo distance query.
     *
     * @param string       $key
     * @param array|string $location
     * @param string       $distance
     *
     * @return GeoDistance
     */
    public function geo_distance($key, $location, $distance)
    {
        return new GeoDistance($key, $location, $distance);
    }
}
