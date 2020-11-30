<?php
namespace Webonyx\Elastica3x\Filter;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Multi Abstract filter object. Should be extended by filter types composed of an array of sub filters.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
abstract class AbstractMulti extends AbstractFilter
{
    /**
     * Filters.
     *
     * @var array
     */
    protected $_filters = [];

    /**
     * @param array $filters
     */
    public function __construct(array $filters = [])
    {
        if (!empty($filters)) {
            $this->setFilters($filters);
        }
    }

    /**
     * Add filter.
     *
     * @param \Webonyx\Elastica3x\Filter\AbstractFilter $filter
     *
     * @return $this
     */
    public function addFilter(AbstractFilter $filter)
    {
        $this->_filters[] = $filter;

        return $this;
    }

    /**
     * Set filters.
     *
     * @param array $filters
     *
     * @return $this
     */
    public function setFilters(array $filters)
    {
        $this->_filters = [];

        foreach ($filters as $filter) {
            $this->addFilter($filter);
        }

        return $this;
    }

    /**
     * @return array Filters
     */
    public function getFilters()
    {
        return $this->_filters;
    }

    /**
     * @see \Webonyx\Elastica3x\Param::toArray()
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();
        $name = $this->_getBaseName();
        $filterData = $data[$name];

        if (empty($filterData)) {
            $filterData = $this->_filters;
        } else {
            $filterData['filters'] = $this->_filters;
        }

        $data[$name] = $filterData;

        return $this->_convertArrayable($data);
    }
}
