<?php
namespace Webonyx\Elastica3x\Filter;

use Webonyx\Elastica3x\Query\AbstractQuery;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Nested filter.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-nested-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class Nested extends AbstractFilter
{
    /**
     * Adds field to mlt filter.
     *
     * @param string $path Nested object path
     *
     * @return $this
     */
    public function setPath($path)
    {
        return $this->setParam('path', $path);
    }

    /**
     * Sets nested query.
     *
     * @param \Webonyx\Elastica3x\Query\AbstractQuery $query
     *
     * @return $this
     */
    public function setQuery(AbstractQuery $query)
    {
        return $this->setParam('query', $query);
    }

    /**
     * Sets nested filter.
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
     * Set join option.
     *
     * @param bool $join
     *
     * @return $this
     */
    public function setJoin($join)
    {
        return $this->setParam('join', (bool) $join);
    }
}
