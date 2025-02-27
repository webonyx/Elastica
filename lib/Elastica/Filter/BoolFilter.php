<?php
namespace Webonyx\Elastica3x\Filter;

use Webonyx\Elastica3x\Exception\InvalidException;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Bool Filter.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class BoolFilter extends AbstractFilter
{
    /**
     * Must.
     *
     * @var array
     */
    protected $_must = [];

    /**
     * Should.
     *
     * @var array
     */
    protected $_should = [];

    /**
     * Must not.
     *
     * @var array
     */
    protected $_mustNot = [];

    /**
     * Adds should filter.
     *
     * @param array|\Webonyx\Elastica3x\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addShould($args)
    {
        return $this->_addFilter('should', $args);
    }

    /**
     * Adds must filter.
     *
     * @param array|\Webonyx\Elastica3x\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addMust($args)
    {
        return $this->_addFilter('must', $args);
    }

    /**
     * Adds mustNot filter.
     *
     * @param array|\Webonyx\Elastica3x\Filter\AbstractFilter $args Filter data
     *
     * @return $this
     */
    public function addMustNot($args)
    {
        return $this->_addFilter('mustNot', $args);
    }

    /**
     * Adds general filter based on type.
     *
     * @param string                                $type Filter type
     * @param array|\Webonyx\Elastica3x\Filter\AbstractFilter $args Filter data
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return $this
     */
    protected function _addFilter($type, $args)
    {
        if (!is_array($args) && !($args instanceof AbstractFilter)) {
            throw new InvalidException('Invalid parameter. Has to be array or instance of Webonyx\Elastica3x\Filter');
        }

        if (is_array($args)) {
            $parsedArgs = [];

            foreach ($args as $filter) {
                if ($filter instanceof AbstractFilter) {
                    $parsedArgs[] = $filter;
                }
            }

            $args = $parsedArgs;
        }

        $varName = '_'.$type;
        $this->{$varName}[] = $args;

        return $this;
    }

    /**
     * Converts bool filter to array.
     *
     * @see \Webonyx\Elastica3x\Filter\AbstractFilter::toArray()
     *
     * @return array Filter array
     */
    public function toArray()
    {
        $args = [];

        if (!empty($this->_must)) {
            $args['bool']['must'] = $this->_must;
        }

        if (!empty($this->_should)) {
            $args['bool']['should'] = $this->_should;
        }

        if (!empty($this->_mustNot)) {
            $args['bool']['must_not'] = $this->_mustNot;
        }

        if (isset($args['bool'])) {
            $args['bool'] = array_merge($args['bool'], $this->getParams());
        }

        return $this->_convertArrayable($args);
    }
}
