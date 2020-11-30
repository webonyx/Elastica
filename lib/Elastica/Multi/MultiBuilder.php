<?php
namespace Webonyx\Elastica3x\Multi;

use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\Search as BaseSearch;

class MultiBuilder implements MultiBuilderInterface
{
    /**
     * @param Response     $response
     * @param BaseSearch[] $searches
     *
     * @return ResultSet
     */
    public function buildMultiResultSet(Response $response, $searches)
    {
        $resultSets = $this->buildResultSets($response, $searches);

        return new ResultSet($response, $resultSets);
    }

    /**
     * @param Response   $childResponse
     * @param BaseSearch $search
     *
     * @return \Webonyx\Elastica3x\ResultSet
     */
    private function buildResultSet(Response $childResponse, BaseSearch $search)
    {
        return $search->getResultSetBuilder()->buildResultSet($childResponse, $search->getQuery());
    }

    /**
     * @param Response     $response
     * @param BaseSearch[] $searches
     *
     * @return \Webonyx\Elastica3x\ResultSet[]
     */
    private function buildResultSets(Response $response, $searches)
    {
        $data = $response->getData();
        if (!isset($data['responses']) || !is_array($data['responses'])) {
            return [];
        }

        $resultSets = [];
        reset($searches);

        foreach ($data['responses'] as $responseData) {
            list($key, $search) = each($searches);

            $resultSets[$key] = $this->buildResultSet(new Response($responseData), $search);
        }

        return $resultSets;
    }
}
