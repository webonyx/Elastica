<?php
namespace Webonyx\Elastica3x\Bulk;

use Webonyx\Elastica3x\Response as BaseResponse;

class ResponseSet extends BaseResponse implements \Iterator, \Countable
{
    /**
     * @var \Webonyx\Elastica3x\Bulk\Response[]
     */
    protected $_bulkResponses = [];

    /**
     * @var int
     */
    protected $_position = 0;

    /**
     * @param \Webonyx\Elastica3x\Response        $response
     * @param \Webonyx\Elastica3x\Bulk\Response[] $bulkResponses
     */
    public function __construct(BaseResponse $response, array $bulkResponses)
    {
        parent::__construct($response->getData());

        $this->_bulkResponses = $bulkResponses;
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\Response[]
     */
    public function getBulkResponses()
    {
        return $this->_bulkResponses;
    }

    /**
     * Returns first found error.
     *
     * @return string
     */
    public function getError()
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return $bulkResponse->getError();
            }
        }

        return '';
    }

    /**
     * Returns first found error (full array).
     *
     * @return array|string
     */
    public function getFullError()
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return $bulkResponse->getFullError();
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isOk()
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if (!$bulkResponse->isOk()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        foreach ($this->getBulkResponses() as $bulkResponse) {
            if ($bulkResponse->hasError()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool|\Webonyx\Elastica3x\Bulk\Response
     */
    public function current()
    {
        return $this->valid()
            ? $this->_bulkResponses[$this->key()]
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
        return isset($this->_bulkResponses[$this->key()]);
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
        return count($this->_bulkResponses);
    }
}
