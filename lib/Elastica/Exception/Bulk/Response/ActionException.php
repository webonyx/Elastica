<?php
namespace Webonyx\Elastica3x\Exception\Bulk\Response;

use Webonyx\Elastica3x\Bulk\Response;
use Webonyx\Elastica3x\Exception\BulkException;

class ActionException extends BulkException
{
    /**
     * @var \Webonyx\Elastica3x\Response
     */
    protected $_response;

    /**
     * @param \Webonyx\Elastica3x\Bulk\Response $response
     */
    public function __construct(Response $response)
    {
        $this->_response = $response;

        parent::__construct($this->getErrorMessage($response));
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\Action
     */
    public function getAction()
    {
        return $this->getResponse()->getAction();
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * @param \Webonyx\Elastica3x\Bulk\Response $response
     *
     * @return string
     */
    public function getErrorMessage(Response $response)
    {
        $error = $response->getError();
        $opType = $response->getOpType();
        $data = $response->getData();

        $path = '';
        if (isset($data['_index'])) {
            $path .= '/'.$data['_index'];
        }
        if (isset($data['_type'])) {
            $path .= '/'.$data['_type'];
        }
        if (isset($data['_id'])) {
            $path .= '/'.$data['_id'];
        }
        $message = "$opType: $path caused $error";

        return $message;
    }
}
