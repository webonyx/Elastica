<?php
namespace Webonyx\Elastica3x\Exception;

use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Response;

/**
 * Connection exception.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class ConnectionException extends \RuntimeException implements ExceptionInterface
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
     * @param string             $message  Message
     * @param \Webonyx\Elastica3x\Request  $request
     * @param \Webonyx\Elastica3x\Response $response
     */
    public function __construct($message, Request $request = null, Response $response = null)
    {
        $this->_request = $request;
        $this->_response = $response;

        parent::__construct($message);
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
}
