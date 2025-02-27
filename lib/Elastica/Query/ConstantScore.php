<?php
namespace Webonyx\Elastica3x\Query;

use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Filter\AbstractFilter;

/**
 * Constant score query.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-constant-score-query.html
 */
class ConstantScore extends AbstractQuery
{
    /**
     * Construct constant score query.
     *
     * @param null|\Webonyx\Elastica3x\Query\AbstractQuery|array $filter
     */
    public function __construct($filter = null)
    {
        if (!is_null($filter)) {
            if ($filter instanceof AbstractFilter) {
                trigger_error('Deprecated: Webonyx\Elastica3x\Query\ConstantScore passing AbstractFilter is deprecated. Pass AbstractQuery instead.', E_USER_DEPRECATED);
            } elseif (!is_array($filter) && !($filter instanceof AbstractQuery)) {
                throw new InvalidException('Filter must be instance of AbstractQuery');
            }

            $this->setFilter($filter);
        }
    }

    /**
     * Set filter.
     *
     * @param array|\Webonyx\Elastica3x\Query\AbstractQuery $filter
     *
     * @return $this
     */
    public function setFilter($filter)
    {
        if ($filter instanceof AbstractFilter) {
            trigger_error('Deprecated: Webonyx\Elastica3x\Query\ConstantScore::setFilter passing AbstractFilter is deprecated. Pass AbstractQuery instead.', E_USER_DEPRECATED);
        } elseif (!is_array($filter) && !($filter instanceof AbstractQuery)) {
            throw new InvalidException('Filter must be instance of AbstractQuery or array');
        }

        return $this->setParam('filter', $filter);
    }

    /**
     * Set query.
     *
     * @param array|\Webonyx\Elastica3x\Query\AbstractQuery $query
     *
     * @throws InvalidException If query is not an array or instance of AbstractQuery
     *
     * @return $this
     */
    public function setQuery($query)
    {
        if (!is_array($query) && !($query instanceof AbstractQuery)) {
            throw new InvalidException('Invalid parameter. Has to be array or instance of Webonyx\Elastica3x\Query\AbstractQuery');
        }

        return $this->setParam('query', $query);
    }

    /**
     * Set boost.
     *
     * @param float $boost
     *
     * @return $this
     */
    public function setBoost($boost)
    {
        return $this->setParam('boost', $boost);
    }
}
