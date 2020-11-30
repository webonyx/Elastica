<?php
namespace Webonyx\Elastica3x\Transport;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Exception\PartialShardFailureException;
use Webonyx\Elastica3x\Exception\ResponseException;
use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Request as Webonyx\Elastica3xRequest;
use Webonyx\Elastica3x\Response as Webonyx\Elastica3xResponse;
use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\Request as HttpAdapterRequest;
use Ivory\HttpAdapter\Message\Response as HttpAdapterResponse;
use Ivory\HttpAdapter\Message\Stream\StringStream;

class HttpAdapter extends AbstractTransport
{
    /**
     * @var HttpAdapterInterface
     */
    private $httpAdapter;

    /**
     * @var string
     */
    private $_scheme = 'http';

    /**
     * Construct transport.
     *
     * @param Connection           $connection
     * @param HttpAdapterInterface $httpAdapter
     */
    public function __construct(Connection $connection = null, HttpAdapterInterface $httpAdapter)
    {
        parent::__construct($connection);
        $this->httpAdapter = $httpAdapter;
    }

    /**
     * Makes calls to the elasticsearch server.
     *
     * All calls that are made to the server are done through this function
     *
     * @param \Webonyx\Elastica3x\Request $elasticaRequest
     * @param array             $params          Host, Port, ...
     *
     * @throws \Webonyx\Elastica3x\Exception\ConnectionException
     * @throws \Webonyx\Elastica3x\Exception\ResponseException
     * @throws \Webonyx\Elastica3x\Exception\Connection\HttpException
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function exec(Webonyx\Elastica3xRequest $elasticaRequest, array $params)
    {
        $connection = $this->getConnection();

        if ($timeout = $connection->getTimeout()) {
            $this->httpAdapter->getConfiguration()->setTimeout($timeout);
        }

        $httpAdapterRequest = $this->_createHttpAdapterRequest($elasticaRequest, $connection);

        $start = microtime(true);
        $httpAdapterResponse = $this->httpAdapter->sendRequest($httpAdapterRequest);
        $end = microtime(true);

        $elasticaResponse = $this->_createWebonyx\Elastica3xResponse($httpAdapterResponse);
        $elasticaResponse->setQueryTime($end - $start);

        $elasticaResponse->setTransferInfo(
            [
                'request_header' => $httpAdapterRequest->getMethod(),
                'http_code' => $httpAdapterResponse->getStatusCode(),
            ]
        );

        if ($elasticaResponse->hasError()) {
            throw new ResponseException($elasticaRequest, $elasticaResponse);
        }

        if ($elasticaResponse->hasFailedShards()) {
            throw new PartialShardFailureException($elasticaRequest, $elasticaResponse);
        }

        return $elasticaResponse;
    }

    /**
     * @param HttpAdapterResponse $httpAdapterResponse
     *
     * @return Webonyx\Elastica3xResponse
     */
    protected function _createWebonyx\Elastica3xResponse(HttpAdapterResponse $httpAdapterResponse)
    {
        return new Webonyx\Elastica3xResponse((string) $httpAdapterResponse->getBody(), $httpAdapterResponse->getStatusCode());
    }

    /**
     * @param Webonyx\Elastica3xRequest $elasticaRequest
     * @param Connection      $connection
     *
     * @return HttpAdapterRequest
     */
    protected function _createHttpAdapterRequest(Webonyx\Elastica3xRequest $elasticaRequest, Connection $connection)
    {
        $data = $elasticaRequest->getData();
        $body = null;
        $method = $elasticaRequest->getMethod();
        $headers = $connection->hasConfig('headers') ?: [];
        if (!empty($data) || '0' === $data) {
            if ($method == Webonyx\Elastica3xRequest::GET) {
                $method = Webonyx\Elastica3xRequest::POST;
            }

            if ($this->hasParam('postWithRequestBody') && $this->getParam('postWithRequestBody') == true) {
                $elasticaRequest->setMethod(Webonyx\Elastica3xRequest::POST);
                $method = Webonyx\Elastica3xRequest::POST;
            }

            if (is_array($data)) {
                $body = JSON::stringify($data, JSON_UNESCAPED_UNICODE);
            } else {
                $body = $data;
            }
        }

        $url = $this->_getUri($elasticaRequest, $connection);
        $streamBody = new StringStream($body);

        return new HttpAdapterRequest($url, $method, HttpAdapterRequest::PROTOCOL_VERSION_1_1, $headers, $streamBody);
    }

    /**
     * @param Webonyx\Elastica3xRequest      $request
     * @param \Webonyx\Elastica3x\Connection $connection
     *
     * @return string
     */
    protected function _getUri(Webonyx\Elastica3xRequest $request, Connection $connection)
    {
        $url = $connection->hasConfig('url') ? $connection->getConfig('url') : '';

        if (!empty($url)) {
            $baseUri = $url;
        } else {
            $baseUri = $this->_scheme.'://'.$connection->getHost().':'.$connection->getPort().'/'.$connection->getPath();
        }

        $baseUri .= $request->getPath();

        $query = $request->getQuery();

        if (!empty($query)) {
            $baseUri .= '?'.http_build_query($query);
        }

        return $baseUri;
    }
}
