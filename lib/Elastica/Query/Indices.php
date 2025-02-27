<?php
namespace Webonyx\Elastica3x\Query;

use Webonyx\Elastica3x\Index;

/**
 * Class Indices.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-indices-query.html
 */
class Indices extends AbstractQuery
{
    /**
     * @param AbstractQuery $query   Query which will be applied to docs in the specified indices
     * @param mixed[]       $indices
     */
    public function __construct(AbstractQuery $query, array $indices)
    {
        $this->setIndices($indices)->setQuery($query);
    }

    /**
     * Set the indices on which this query should be applied.
     *
     * @param mixed[] $indices
     *
     * @return $this
     */
    public function setIndices(array $indices)
    {
        $this->setParam('indices', []);
        foreach ($indices as $index) {
            $this->addIndex($index);
        }

        return $this;
    }

    /**
     * Adds one more index on which this query should be applied.
     *
     * @param string|\Webonyx\Elastica3x\Index $index
     *
     * @return $this
     */
    public function addIndex($index)
    {
        if ($index instanceof Index) {
            $index = $index->getName();
        }

        return $this->addParam('indices', (string) $index);
    }

    /**
     * Set the query to be applied to docs in the specified indices.
     *
     * @param AbstractQuery $query
     *
     * @return $this
     */
    public function setQuery(AbstractQuery $query)
    {
        return $this->setParam('query', $query);
    }

    /**
     * Set the query to be applied to docs in indices which do not match those specified in the "indices" parameter.
     *
     * @param AbstractQuery $query
     *
     * @return $this
     */
    public function setNoMatchQuery(AbstractQuery $query)
    {
        return $this->setParam('no_match_query', $query);
    }
}
