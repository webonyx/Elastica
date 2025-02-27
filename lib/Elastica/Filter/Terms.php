<?php
namespace Webonyx\Elastica3x\Filter;

use Webonyx\Elastica3x\Exception\InvalidException;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Terms filter.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-terms-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class Terms extends AbstractFilter
{
    /**
     * Terms.
     *
     * @var array Terms
     */
    protected $_terms = [];

    /**
     * Terms key.
     *
     * @var string Terms key
     */
    protected $_key = '';

    /**
     * Creates terms filter.
     *
     * @param string $key   Terms key
     * @param array  $terms Terms values
     */
    public function __construct($key = '', array $terms = [])
    {
        $this->setTerms($key, $terms);
    }

    /**
     * Sets key and terms for the filter.
     *
     * @param string $key   Terms key
     * @param array  $terms Terms for the query.
     *
     * @return $this
     */
    public function setTerms($key, array $terms)
    {
        $this->_key = $key;
        $this->_terms = array_values($terms);

        return $this;
    }

    /**
     * Set the lookup parameters for this filter.
     *
     * @param string                       $key     terms key
     * @param string|\Webonyx\Elastica3x\Type        $type    document type from which to fetch the terms values
     * @param string                       $id      id of the document from which to fetch the terms values
     * @param string                       $path    the field from which to fetch the values for the filter
     * @param string|array|\Webonyx\Elastica3x\Index $options An array of options or the index from which to fetch the terms values. Defaults to the current index.
     *
     * @return $this
     */
    public function setLookup($key, $type, $id, $path, $options = [])
    {
        $this->_key = $key;
        if ($type instanceof \Webonyx\Elastica3x\Type) {
            $type = $type->getName();
        }
        $this->_terms = [
            'type' => $type,
            'id' => $id,
            'path' => $path,
        ];

        $index = $options;
        if (is_array($options)) {
            if (isset($options['index'])) {
                $index = $options['index'];
                unset($options['index']);
            }
            $this->_terms = array_merge($options, $this->_terms);
        }

        if (!is_null($index)) {
            if ($index instanceof \Webonyx\Elastica3x\Index) {
                $index = $index->getName();
            }
            $this->_terms['index'] = $index;
        }

        return $this;
    }

    /**
     * Adds an additional term to the query.
     *
     * @param string $term Filter term
     *
     * @return $this
     */
    public function addTerm($term)
    {
        $this->_terms[] = $term;

        return $this;
    }

    /**
     * Converts object to an array.
     *
     * @see \Webonyx\Elastica3x\Filter\AbstractFilter::toArray()
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return array
     */
    public function toArray()
    {
        if (empty($this->_key)) {
            throw new InvalidException('Terms key has to be set');
        }
        $this->_params[$this->_key] = $this->_terms;

        return ['terms' => $this->_params];
    }

    /**
     * Set execution mode.
     *
     * @param string $execution Options: "bool", "and", "or", "plain" or "fielddata"
     *
     * @return $this
     */
    public function setExecution($execution)
    {
        return $this->setParam('execution', (string) $execution);
    }
}
