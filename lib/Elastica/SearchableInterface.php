<?php
namespace Webonyx\Elastica3x;

/**
 * Webonyx\Elastica3x searchable interface.
 *
 * @author Thibault Duplessis <thibault.duplessis@gmail.com>
 */
interface SearchableInterface
{
    /**
     * Searches results for a query.
     *
     * TODO: Improve sample code
     * {
     *     "from" : 0,
     *     "size" : 10,
     *     "sort" : {
     *          "postDate" : {"reverse" : true},
     *          "user" : { },
     *          "_score" : { }
     *      },
     *      "query" : {
     *          "term" : { "user" : "kimchy" }
     *      }
     * }
     *
     * @param string|array|\Webonyx\Elastica3x\Query $query   Array with all query data inside or a Webonyx\Elastica3x\Query object
     * @param null                         $options
     *
     * @return \Webonyx\Elastica3x\ResultSet with all results inside
     */
    public function search($query = '', $options = null);

    /**
     * Counts results for a query.
     *
     * If no query is set, matchall query is created
     *
     * @param string|array|\Webonyx\Elastica3x\Query $query Array with all query data inside or a Webonyx\Elastica3x\Query object
     *
     * @return int number of documents matching the query
     */
    public function count($query = '');

    /**
     * @param \Webonyx\Elastica3x\Query|string $query
     * @param array                  $options
     *
     * @return \Webonyx\Elastica3x\Search
     */
    public function createSearch($query = '', $options = null);
}
