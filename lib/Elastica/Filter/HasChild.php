<?php
namespace Webonyx\Elastica3x\Filter;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Returns parent documents having child docs matching the query.
 *
 * @author Fabian Vogler <fabian@equivalence.ch>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-has-child-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class HasChild extends AbstractFilter
{
    /**
     * Construct HasChild filter.
     *
     * @param string|\Webonyx\Elastica3x\Query|\Webonyx\Elastica3x\Filter\AbstractFilter $query Query string or a Webonyx\Elastica3x\Query object or a filter
     * @param string|\Webonyx\Elastica3x\Type                                  $type  Child document type
     */
    public function __construct($query, $type = null)
    {
        $this->setType($type);
        if ($query instanceof AbstractFilter) {
            $this->setFilter($query);
        } else {
            $this->setQuery($query);
        }
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
     * Sets the filter object.
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
     * Set type of the child document.
     *
     * @param string|\Webonyx\Elastica3x\Type $type Child document type
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
     * Set minimum number of children are required to match for the parent doc to be considered a match.
     *
     * @param int $count
     *
     * @return $this
     */
    public function setMinimumChildrenCount($count)
    {
        return $this->setParam('min_children', (int) $count);
    }

    /**
     * Set maximum number of children are required to match for the parent doc to be considered a match.
     *
     * @param int $count
     *
     * @return $this
     */
    public function setMaximumChildrenCount($count)
    {
        return $this->setParam('max_children', (int) $count);
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
