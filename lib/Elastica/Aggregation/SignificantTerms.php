<?php
namespace Webonyx\Elastica3x\Aggregation;

use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Filter\AbstractFilter;
use Webonyx\Elastica3x\Query\AbstractQuery;

/**
 * Class SignificantTerms.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-significantterms-aggregation.html
 */
class SignificantTerms extends AbstractTermsAggregation
{
    /**
     * The default source of statistical information for background term frequencies is the entire index and this scope can
     * be narrowed through the use of a background_filter to focus in on significant terms within a narrower context.
     *
     * @param AbstractQuery $filter
     *
     * @return $this
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-significantterms-aggregation.html#_custom_background_context
     */
    public function setBackgroundFilter($filter)
    {
        if ($filter instanceof AbstractFilter) {
            trigger_error('Deprecated: Webonyx\Elastica3x\Aggregation\SignificantTerms::setBackgroundFilter passing filter as AbstractFilter is deprecated. Pass instance of AbstractQuery instead.', E_USER_DEPRECATED);
        } elseif (!($filter instanceof AbstractQuery)) {
            throw new InvalidException('Filter must be instance of AbstractQuery');
        }

        return $this->setParam('background_filter', $filter);
    }
}
