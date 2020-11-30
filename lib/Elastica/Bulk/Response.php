<?php
namespace Webonyx\Elastica3x\Bulk;

use Webonyx\Elastica3x\Response as BaseResponse;

class Response extends BaseResponse
{
    /**
     * @var \Webonyx\Elastica3x\Bulk\Action
     */
    protected $_action;

    /**
     * @var string
     */
    protected $_opType;

    /**
     * @param array|string          $responseData
     * @param \Webonyx\Elastica3x\Bulk\Action $action
     * @param string                $opType
     */
    public function __construct($responseData, Action $action, $opType)
    {
        parent::__construct($responseData);

        $this->_action = $action;
        $this->_opType = $opType;
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\Action
     */
    public function getAction()
    {
        return $this->_action;
    }

    /**
     * @return string
     */
    public function getOpType()
    {
        return $this->_opType;
    }
}
