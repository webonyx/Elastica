<?php
namespace Webonyx\Elastica3x;

use Webonyx\Elastica3x\Bulk\Action;
use Webonyx\Elastica3x\Bulk\Action\AbstractDocument as AbstractDocumentAction;
use Webonyx\Elastica3x\Bulk\Response as BulkResponse;
use Webonyx\Elastica3x\Bulk\ResponseSet;
use Webonyx\Elastica3x\Exception\Bulk\ResponseException as BulkResponseException;
use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Script\AbstractScript;

class Bulk
{
    const DELIMITER = "\n";

    /**
     * @var \Webonyx\Elastica3x\Client
     */
    protected $_client;

    /**
     * @var \Webonyx\Elastica3x\Bulk\Action[]
     */
    protected $_actions = [];

    /**
     * @var string|null
     */
    protected $_index;

    /**
     * @var string|null
     */
    protected $_type;

    /**
     * @var array request parameters to the bulk api
     */
    protected $_requestParams = [];

    /**
     * @param \Webonyx\Elastica3x\Client $client
     */
    public function __construct(Client $client)
    {
        $this->_client = $client;
    }

    /**
     * @param string|\Webonyx\Elastica3x\Index $index
     *
     * @return $this
     */
    public function setIndex($index)
    {
        if ($index instanceof Index) {
            $index = $index->getName();
        }

        $this->_index = (string) $index;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * @return bool
     */
    public function hasIndex()
    {
        return null !== $this->getIndex() && '' !== $this->getIndex();
    }

    /**
     * @param string|\Webonyx\Elastica3x\Type $type
     *
     * @return $this
     */
    public function setType($type)
    {
        if ($type instanceof Type) {
            $this->setIndex($type->getIndex()->getName());
            $type = $type->getName();
        }

        $this->_type = (string) $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->_type;
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return null !== $this->getType() && '' !== $this->getType();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        $path = '';
        if ($this->hasIndex()) {
            $path .= $this->getIndex().'/';
            if ($this->hasType()) {
                $path .= $this->getType().'/';
            }
        }
        $path .= '_bulk';

        return $path;
    }

    /**
     * @param \Webonyx\Elastica3x\Bulk\Action $action
     *
     * @return $this
     */
    public function addAction(Action $action)
    {
        $this->_actions[] = $action;

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Bulk\Action[] $actions
     *
     * @return $this
     */
    public function addActions(array $actions)
    {
        foreach ($actions as $action) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\Action[]
     */
    public function getActions()
    {
        return $this->_actions;
    }

    /**
     * @param \Webonyx\Elastica3x\Document $document
     * @param string             $opType
     *
     * @return $this
     */
    public function addDocument(Document $document, $opType = null)
    {
        $action = AbstractDocumentAction::create($document, $opType);

        return $this->addAction($action);
    }

    /**
     * @param \Webonyx\Elastica3x\Document[] $documents
     * @param string               $opType
     *
     * @return $this
     */
    public function addDocuments(array $documents, $opType = null)
    {
        foreach ($documents as $document) {
            $this->addDocument($document, $opType);
        }

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Script\AbstractScript $script
     * @param string                          $opType
     *
     * @return $this
     */
    public function addScript(AbstractScript $script, $opType = null)
    {
        $action = AbstractDocumentAction::create($script, $opType);

        return $this->addAction($action);
    }

    /**
     * @param \Webonyx\Elastica3x\Document[] $scripts
     * @param string               $opType
     *
     * @return $this
     */
    public function addScripts(array $scripts, $opType = null)
    {
        foreach ($scripts as $document) {
            $this->addScript($document, $opType);
        }

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Script\AbstractScript|\Webonyx\Elastica3x\Document|array $data
     * @param string                                                   $opType
     *
     * @return $this
     */
    public function addData($data, $opType = null)
    {
        if (!is_array($data)) {
            $data = [$data];
        }

        foreach ($data as $actionData) {
            if ($actionData instanceof AbstractScript) {
                $this->addScript($actionData, $opType);
            } elseif ($actionData instanceof Document) {
                $this->addDocument($actionData, $opType);
            } else {
                throw new \InvalidArgumentException('Data should be a Document, a Script or an array containing Documents and/or Scripts');
            }
        }

        return $this;
    }

    /**
     * @param array $data
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return $this
     */
    public function addRawData(array $data)
    {
        foreach ($data as $row) {
            if (is_array($row)) {
                $opType = key($row);
                $metadata = reset($row);
                if (Action::isValidOpType($opType)) {
                    // add previous action
                    if (isset($action)) {
                        $this->addAction($action);
                    }
                    $action = new Action($opType, $metadata);
                } elseif (isset($action)) {
                    $action->setSource($row);
                    $this->addAction($action);
                    $action = null;
                } else {
                    throw new InvalidException('Invalid bulk data, source must follow action metadata');
                }
            } else {
                throw new InvalidException('Invalid bulk data, should be array of array, Document or Bulk/Action');
            }
        }

        // add last action if available
        if (isset($action)) {
            $this->addAction($action);
        }

        return $this;
    }

    /**
     * Set a url parameter on the request bulk request.
     *
     * @param string $name  name of the parameter
     * @param string $value value of the parameter
     *
     * @return $this
     */
    public function setRequestParam($name, $value)
    {
        $this->_requestParams[$name] = $value;

        return $this;
    }

    /**
     * Set the amount of time that the request will wait the shards to come on line.
     * Requires Elasticsearch version >= 0.90.8.
     *
     * @param string $time timeout in Elasticsearch time format
     *
     * @return $this
     */
    public function setShardTimeout($time)
    {
        return $this->setRequestParam('timeout', $time);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @return string
     */
    public function toString()
    {
        $data = '';
        foreach ($this->getActions() as $action) {
            $data .= $action->toString();
        }

        return $data;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $data = [];
        foreach ($this->getActions() as $action) {
            foreach ($action->toArray() as $row) {
                $data[] = $row;
            }
        }

        return $data;
    }

    /**
     * @return \Webonyx\Elastica3x\Bulk\ResponseSet
     */
    public function send()
    {
        $path = $this->getPath();
        $data = $this->toString();

        $response = $this->_client->request($path, Request::POST, $data, $this->_requestParams);

        return $this->_processResponse($response);
    }

    /**
     * @param \Webonyx\Elastica3x\Response $response
     *
     * @throws \Webonyx\Elastica3x\Exception\Bulk\ResponseException
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return \Webonyx\Elastica3x\Bulk\ResponseSet
     */
    protected function _processResponse(Response $response)
    {
        $responseData = $response->getData();

        $actions = $this->getActions();

        $bulkResponses = [];

        if (isset($responseData['items']) && is_array($responseData['items'])) {
            foreach ($responseData['items'] as $key => $item) {
                if (!isset($actions[$key])) {
                    throw new InvalidException('No response found for action #'.$key);
                }

                $action = $actions[$key];

                $opType = key($item);
                $bulkResponseData = reset($item);

                if ($action instanceof AbstractDocumentAction) {
                    $data = $action->getData();
                    if ($data instanceof Document && $data->isAutoPopulate()
                        || $this->_client->getConfigValue(['document', 'autoPopulate'], false)
                    ) {
                        if (!$data->hasId() && isset($bulkResponseData['_id'])) {
                            $data->setId($bulkResponseData['_id']);
                        }
                        if (isset($bulkResponseData['_version'])) {
                            $data->setVersion($bulkResponseData['_version']);
                        }
                    }
                }

                $bulkResponses[] = new BulkResponse($bulkResponseData, $action, $opType);
            }
        }

        $bulkResponseSet = new ResponseSet($response, $bulkResponses);

        if ($bulkResponseSet->hasError()) {
            throw new BulkResponseException($bulkResponseSet);
        }

        return $bulkResponseSet;
    }
}
