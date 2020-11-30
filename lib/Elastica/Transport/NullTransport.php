<?php
namespace Webonyx\Elastica3x\Transport;

use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Response;

/**
 * Webonyx\Elastica3x Null Transport object.
 *
 * This is used in case you just need a test transport that doesn't do any connection to an elasticsearch
 * host but still returns a valid response object
 *
 * @author James Boehmer <james.boehmer@jamesboehmer.com>
 */
class NullTransport extends AbstractTransport
{
    /**
     * Null transport.
     *
     * @param \Webonyx\Elastica3x\Request $request
     * @param array             $params  Hostname, port, path, ...
     *
     * @return \Webonyx\Elastica3x\Response Response empty object
     */
    public function exec(Request $request, array $params)
    {
        $response = [
            'took' => 0,
            'timed_out' => false,
            '_shards' => [
                'total' => 0,
                'successful' => 0,
                'failed' => 0,
            ],
            'hits' => [
                'total' => 0,
                'max_score' => null,
                'hits' => [],
            ],
            'params' => $params,
        ];

        return new Response(JSON::stringify($response));
    }
}
