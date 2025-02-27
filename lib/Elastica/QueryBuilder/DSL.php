<?php
namespace Webonyx\Elastica3x\QueryBuilder;

/**
 * DSL Interface.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@googlemail.com>
 */
interface DSL
{
    const TYPE_QUERY = 'query';
    const TYPE_FILTER = 'filter';
    const TYPE_AGGREGATION = 'aggregation';
    const TYPE_SUGGEST = 'suggest';

    /**
     * must return type for QueryBuilder usage.
     *
     * @return string
     */
    public function getType();
}
