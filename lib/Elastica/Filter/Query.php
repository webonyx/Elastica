<?php
namespace Webonyx\Elastica3x\Filter;

use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Query\AbstractQuery;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Query filter.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-query-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class Query extends AbstractFilter
{
    /**
     * Query.
     *
     * @var array
     */
    protected $_query;

    /**
     * Construct query filter.
     *
     * @param array|\Webonyx\Elastica3x\Query\AbstractQuery $query
     */
    public function __construct($query = null)
    {
        if (!is_null($query)) {
            $this->setQuery($query);
        }
    }

    /**
     * Set query.
     *
     * @param array|\Webonyx\Elastica3x\Query\AbstractQuery $query
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException If parameter is invalid
     *
     * @return $this
     */
    public function setQuery($query)
    {
        if (!$query instanceof AbstractQuery && !is_array($query)) {
            throw new InvalidException('expected an array or instance of Webonyx\Elastica3x\Query\AbstractQuery');
        }

        $this->_query = $query;

        return $this;
    }

    /**
     * @see \Webonyx\Elastica3x\Param::_getBaseName()
     */
    protected function _getBaseName()
    {
        if (empty($this->_params)) {
            return 'query';
        } else {
            return 'fquery';
        }
    }

    /**
     * @see \Webonyx\Elastica3x\Param::toArray()
     */
    public function toArray()
    {
        $data = parent::toArray();

        $name = $this->_getBaseName();
        $filterData = $data[$name];

        if (empty($filterData)) {
            $filterData = $this->_query;
        } else {
            $filterData['query'] = $this->_query;
        }

        $data[$name] = $filterData;

        return $this->_convertArrayable($data);
    }
}
