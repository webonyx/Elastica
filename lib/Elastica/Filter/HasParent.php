<?php
namespace Webonyx\Elastica3x\Filter;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Returns child documents having parent docs matching the query.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-parent-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class HasParent extends AbstractFilter
{
    /**
     * Construct HasParent filter.
     *
     * @param string|\Webonyx\Elastica3x\Query|\Webonyx\Elastica3x\Filter\AbstractFilter $query Query string or a Query object or a filter
     * @param string|\Webonyx\Elastica3x\Type                                  $type  Parent document type
     */
    public function __construct($query, $type)
    {
        if ($query instanceof AbstractFilter) {
            $this->setFilter($query);
        } else {
            $this->setQuery($query);
        }
        $this->setType($type);
    }

    /**
     * Sets query object.
     *
     * @param string|\Webonyx\Elastica3x\Query|\Webonyx\Elastica3x\Query\AbstractQuery $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        return $this->setParam('query', \Webonyx\Elastica3x\Query::create($query));
    }

    /**
     * Sets filter object.
     *
     * @param \Webonyx\Elastica3x\Filter\AbstractFilter $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        return $this->setParam('filter', $filter);
    }

    /**
     * Set type of the parent document.
     *
     * @param string|\Webonyx\Elastica3x\Type $type Parent document type
     *
     * @return $this
     */
    public function setType($type)
    {
        if ($type instanceof \Webonyx\Elastica3x\Type) {
            $type = $type->getName();
        }

        return $this->setParam('type', (string) $type);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        $array = parent::toArray();

        $baseName = $this->_getBaseName();

        if (isset($array[$baseName]['query'])) {
            $array[$baseName]['query'] = $array[$baseName]['query']['query'];
        }

        return $array;
    }
}
