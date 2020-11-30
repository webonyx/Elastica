<?php
namespace Webonyx\Elastica3x\Multi;

use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\Search as BaseSearch;

interface MultiBuilderInterface
{
    /**
     * @param Response     $response
     * @param BaseSearch[] $searches
     *
     * @return ResultSet
     */
    public function buildMultiResultSet(Response $response, $searches);
}
