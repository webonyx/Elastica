<?php
namespace Webonyx\Elastica3x\Exception;

use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Response;

/**
 * Response exception.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class ResponseException extends \RuntimeException implements ExceptionInterface
{
    /**
     * @var \Webonyx\Elastica3x\Request Request object
     */
    protected $_request;

    /**
     * @var \Webonyx\Elastica3x\Response Response object
     */
    protected $_response;

    /**
     * Construct Exception.
     *
     * @param \Webonyx\Elastica3x\Request  $request
     * @param \Webonyx\Elastica3x\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->_request = $request;
        $this->_response = $response;
        parent::__construct($response->getErrorMessage());
    }

    /**
     * Returns request object.
     *
     * @return \Webonyx\Elastica3x\Request Request object
     */
    public function getRequest()
    {
        return $this->_request;
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
     * Returns elasticsearch exception.
     *
     * @return ElasticsearchException
     */
    public function getElasticsearchException()
    {
        $response = $this->getResponse();
        $transfer = $response->getTransferInfo();
        $code = array_key_exists('http_code', $transfer) ? $transfer['http_code'] : 0;

        return new ElasticsearchException($code, $response->getErrorMessage());
    }
}
