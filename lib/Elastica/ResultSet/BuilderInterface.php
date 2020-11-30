<?php
namespace Webonyx\Elastica3x\ResultSet;

use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet;

interface BuilderInterface
{
    /**
     * Builds a ResultSet given a specific response and query.
     *
     * @param Response $response
     * @param Query    $query
     *
     * @return ResultSet
     */
    public function buildResultSet(Response $response, Query $query);
}
