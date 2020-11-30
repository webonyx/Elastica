<?php
namespace Webonyx\Elastica3x\Filter;

trigger_error('Deprecated: Filters are deprecated. Use BoolQuery::addMustNot. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Not Filter.
 *
 * @author Lee Parker, Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-not-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class BoolNot extends AbstractFilter
{
    /**
     * Creates Not filter query.
     *
     * @param \Webonyx\Elastica3x\Filter\AbstractFilter $filter Filter object
     */
    public function __construct(AbstractFilter $filter)
    {
        $this->setFilter($filter);
    }

    /**
     * Set filter.
     *
     * @param \Webonyx\Elastica3x\Filter\AbstractFilter $filter
     *
     * @return $this
     */
    public function setFilter(AbstractFilter $filter)
    {
        return $this->setParam('filter', $filter);
    }

    /**
     * @return string
     */
    protected function _getBaseName()
    {
        return 'not';
    }
}
