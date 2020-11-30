<?php
namespace Webonyx\Elastica3x\Exception;

use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Response;

/**
 * Partial shard failure exception.
 *
 * @author Ian Babrou <ibobrik@gmail.com>
 */
class PartialShardFailureException extends ResponseException
{
    /**
     * Construct Exception.
     *
     * @param \Webonyx\Elastica3x\Request  $request
     * @param \Webonyx\Elastica3x\Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);

        $shardsStatistics = $response->getShardsStatistics();
        $this->message = JSON::stringify($shardsStatistics);
    }
}
