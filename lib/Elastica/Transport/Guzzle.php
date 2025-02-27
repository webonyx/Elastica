<?php
namespace Webonyx\Elastica3x\Transport;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Exception\Connection\GuzzleException;
use Webonyx\Elastica3x\Exception\PartialShardFailureException;
use Webonyx\Elastica3x\Exception\ResponseException;
use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Uri;

/**
 * Webonyx\Elastica3x Guzzle Transport object.
 *
 * @author Milan Magudia <milan@magudia.com>
 */
class Guzzle extends AbstractTransport
{
    /**
     * Http scheme.
     *
     * @var string Http scheme
     */
    protected $_scheme = 'http';

    /**
     * Curl resource to reuse.
     *
     * @var Client Guzzle client to reuse
     */
    protected static $_guzzleClientConnection;

    /**
     * Makes calls to the elasticsearch server.
     *
     * All calls that are made to the server are done through this function
     *
     * @param \Webonyx\Elastica3x\Request $request
     * @param array             $params  Host, Port, ...
     *
     * @throws \Webonyx\Elastica3x\Exception\ConnectionException
     * @throws \Webonyx\Elastica3x\Exception\ResponseException
     * @throws \Webonyx\Elastica3x\Exception\Connection\HttpException
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function exec(Request $request, array $params)
    {
        $connection = $this->getConnection();

        $client = $this->_getGuzzleClient($this->_getBaseUrl($connection), $connection->isPersistent());

        $options = [
            'exceptions' => false, // 4xx and 5xx is expected and NOT an exceptions in this context
        ];
        if ($connection->getTimeout()) {
            $options['timeout'] = $connection->getTimeout();
        }

        $proxy = $connection->getProxy();

        // See: https://github.com/facebook/hhvm/issues/4875
        if (is_null($proxy) && defined('HHVM_VERSION')) {
            $proxy = getenv('http_proxy') ?: null;
        }

        if (!is_null($proxy)) {
            $options['proxy'] = $proxy;
        }

        $req = $this->_createPsr7Request($request, $connection);

        try {
            $start = microtime(true);
            $res = $client->send($req, $options);
            $end = microtime(true);
        } catch (TransferException $ex) {
            throw new GuzzleException($ex, $request, new Response($ex->getMessage()));
        }

        $response = new Response((string) $res->getBody(), $res->getStatusCode());
        $response->setQueryTime($end - $start);
        if ($connection->hasConfig('bigintConversion')) {
            $response->setJsonBigintConversion($connection->getConfig('bigintConversion'));
        }

        $response->setTransferInfo(
            [
                'request_header' => $request->getMethod(),
                'http_code' => $res->getStatusCode(),
            ]
        );

        if ($response->hasError()) {
            throw new ResponseException($request, $response);
        }

        if ($response->hasFailedShards()) {
            throw new PartialShardFailureException($request, $response);
        }

        return $response;
    }

    /**
     * @param Request    $request
     * @param Connection $connection
     *
     * @return Psr7\Request
     */
    protected function _createPsr7Request(Request $request, Connection $connection)
    {
        $req = new Psr7\Request(
            $request->getMethod(),
            $this->_getActionPath($request),
            $connection->hasConfig('headers') && is_array($connection->getConfig('headers'))
                ? $connection->getConfig('headers')
                : []
        );

        $data = $request->getData();
        if (!empty($data) || '0' === $data) {
            if ($req->getMethod() == Request::GET) {
                $req = $req->withMethod(Request::POST);
            }

            if ($this->hasParam('postWithRequestBody') && $this->getParam('postWithRequestBody') == true) {
                $request->setMethod(Request::POST);
                $req = $req->withMethod(Request::POST);
            }

            $req = $req->withBody(
                Psr7\stream_for(is_array($data)
                    ? JSON::stringify($data, JSON_UNESCAPED_UNICODE)
                    : $data
                )
            );
        }

        return $req;
    }

    /**
     * Return Guzzle resource.
     *
     * @param string $baseUrl
     * @param bool   $persistent False if not persistent connection
     *
     * @return Client
     */
    protected function _getGuzzleClient($baseUrl, $persistent = true)
    {
        if (!$persistent || !self::$_guzzleClientConnection) {
            self::$_guzzleClientConnection = new Client(['base_uri' => $baseUrl]);
        }

        return self::$_guzzleClientConnection;
    }

    /**
     * Builds the base url for the guzzle connection.
     *
     * @param \Webonyx\Elastica3x\Connection $connection
     *
     * @return string
     */
    protected function _getBaseUrl(Connection $connection)
    {
        // If url is set, url is taken. Otherwise port, host and path
        $url = $connection->hasConfig('url') ? $connection->getConfig('url') : '';

        if (!empty($url)) {
            $baseUri = $url;
        } else {
            $baseUri = (string) Uri::fromParts([
                'scheme' => $this->_scheme,
                'host' => $connection->getHost(),
                'port' => $connection->getPort(),
                'path' => ltrim('/', $connection->getPath()),
            ]);
        }

        return rtrim($baseUri, '/');
    }

    /**
     * Builds the action path url for each request.
     *
     * @param \Webonyx\Elastica3x\Request $request
     *
     * @return string
     */
    protected function _getActionPath(Request $request)
    {
        $action = $request->getPath();
        if ($action) {
            $action = '/'.ltrim($action, '/');
        }
        $query = $request->getQuery();

        if (!empty($query)) {
            $action .= '?'.http_build_query($query);
        }

        return $action;
    }
}
