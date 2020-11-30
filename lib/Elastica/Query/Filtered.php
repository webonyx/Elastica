<?php
namespace Webonyx\Elastica3x\Query;

use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Filter\AbstractFilter;

trigger_error('Use BoolQuery instead. Filtered query is deprecated since ES 2.0.0-beta1 and this class will be removed in further Webonyx\Elastica3x releases.', E_USER_DEPRECATED);

/**
 * Filtered query. Needs a query and a filter.
 *
 * @deprecated Use BoolQuery instead. Filtered query is deprecated since ES 2.0.0-beta1 and this class will be removed in further Webonyx\Elastica3x releases.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-filtered-query.html
 */
class Filtered extends AbstractQuery
{
    /**
     * Constructs a filtered query.
     *
     * @param \Webonyx\Elastica3x\Query\AbstractQuery $query  OPTIONAL Query object
     * @param \Webonyx\Elastica3x\Query\AbstractQuery $filter OPTIONAL Filter object
     */
    public function __construct(AbstractQuery $query = null, $filter = null)
    {
        $this->setQuery($query);

        if (null !== $filter) {
            if ($filter instanceof AbstractFilter) {
                trigger_error('Deprecated: Webonyx\Elastica3x\Query\Filtered passing AbstractFilter is deprecated. Pass AbstractQuery instead.', E_USER_DEPRECATED);
            } elseif (!($filter instanceof AbstractQuery)) {
                throw new InvalidException('Filter must be instance of AbstractQuery');
            }
        }

        $this->setFilter($filter);
    }

    /**
     * Sets a query.
     *
     * @param \Webonyx\Elastica3x\Query\AbstractQuery $query Query object
     *
     * @return $this
     */
    public function setQuery(AbstractQuery $query = null)
    {
        return $this->setParam('query', $query);
    }

    /**
     * Sets the filter.
     *
     * @param \Webonyx\Elastica3x\Query\AbstractQuery $filter Filter object
     *
     * @return $this
     */
    public function setFilter($filter = null)
    {
        if (null !== $filter) {
            if ($filter instanceof AbstractFilter) {
                trigger_error('Deprecated: Webonyx\Elastica3x\Query\Filtered::setFilter passing AbstractFilter is deprecated. Pass AbstractQuery instead.', E_USER_DEPRECATED);
            } elseif (!($filter instanceof AbstractQuery)) {
                throw new InvalidException('Filter must be instance of AbstractQuery');
            }
        }

        return $this->setParam('filter', $filter);
    }

    /**
     * Gets the filter.
     *
     * @return \Webonyx\Elastica3x\Query\AbstractQuery|\Webonyx\Elastica3x\Filter\AbstractFilter
     */
    public function getFilter()
    {
        return $this->getParam('filter');
    }

    /**
     * Gets the query.
     *
     * @return \Webonyx\Elastica3x\Query\AbstractQuery
     */
    public function getQuery()
    {
        return $this->getParam('query');
    }

    /**
     * Converts query to array.
     *
     * @return array Query array
     *
     * @see \Webonyx\Elastica3x\Query\AbstractQuery::toArray()
     */
    public function toArray()
    {
        $filtered = [];

        if ($this->hasParam('query') && $this->getParam('query') instanceof AbstractQuery) {
            $filtered['query'] = $this->getParam('query')->toArray();
        }

        if ($this->hasParam('filter') && ($this->getParam('filter') instanceof AbstractQuery || $this->getParam('filter') instanceof AbstractFilter)) {
            $filtered['filter'] = $this->getParam('filter')->toArray();
        }

        if (empty($filtered)) {
            throw new InvalidException('A query and/or filter is required');
        }

        return ['filtered' => $filtered];
    }
}
