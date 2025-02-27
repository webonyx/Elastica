<?php
namespace Webonyx\Elastica3x\Multi;

use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet as BaseResultSet;

/**
 * Webonyx\Elastica3x multi search result set
 * List of result sets for each search request.
 *
 * @author munkie
 */
class ResultSet implements \Iterator, \ArrayAccess, \Countable
{
    /**
     * Result Sets.
     *
     * @var array|\Webonyx\Elastica3x\ResultSet[] Result Sets
     */
    protected $_resultSets = [];

    /**
     * Current position.
     *
     * @var int Current position
     */
    protected $_position = 0;

    /**
     * Response.
     *
     * @var \Webonyx\Elastica3x\Response Response object
     */
    protected $_response;

    /**
     * Constructs ResultSet object.
     *
     * @param \Webonyx\Elastica3x\Response $response
     * @param BaseResultSet[]
     */
    public function __construct(Response $response, $resultSets)
    {
        $this->_response = $response;
        $this->_resultSets = $resultSets;
    }

    /**
     * @return array|\Webonyx\Elastica3x\ResultSet[]
     */
    public function getResultSets()
    {
        return $this->_resultSets;
    }

    /**
     * Returns response object.
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * There is at least one result set with error.
     *
     * @return bool
     */
    public function hasError()
    {
        foreach ($this->getResultSets() as $resultSet) {
            if ($resultSet->getResponse()->hasError()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool|\Webonyx\Elastica3x\ResultSet
     */
    public function current()
    {
        return $this->valid()
            ? $this->_resultSets[$this->key()]
            : false;
    }

    /**
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        return isset($this->_resultSets[$this->key()]);
    }

    /**
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_resultSets);
    }

    /**
     * @param string|int $offset
     *
     * @return bool true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->_resultSets[$offset]);
    }

    /**
     * @param mixed $offset
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->_resultSets[$offset]) ? $this->_resultSets[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_resultSets[] = $value;
        } else {
            $this->_resultSets[$offset] = $value;
        }
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->_resultSets[$offset]);
    }
}
