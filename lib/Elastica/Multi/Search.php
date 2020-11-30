<?php
namespace Webonyx\Elastica3x\Multi;

use Webonyx\Elastica3x\Client;
use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Search as BaseSearch;

/**
 * Webonyx\Elastica3x multi search.
 *
 * @author munkie
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-multi-search.html
 */
class Search
{
    /**
     * @var MultiBuilderInterface
     */
    private $_builder;

    /**
     * @var \Webonyx\Elastica3x\Client
     */
    protected $_client;

    /**
     * @var array
     */
    protected $_options = [];

    /**
     * @var array|\Webonyx\Elastica3x\Search[]
     */
    protected $_searches = [];

    /**
     * Constructs search object.
     *
     * @param \Webonyx\Elastica3x\Client      $client  Client object
     * @param MultiBuilderInterface $builder
     */
    public function __construct(Client $client, MultiBuilderInterface $builder = null)
    {
        $this->_builder = $builder ?: new MultiBuilder();
        $this->_client = $client;
    }

    /**
     * @return \Webonyx\Elastica3x\Client
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * @return $this
     */
    public function clearSearches()
    {
        $this->_searches = [];

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Search $search
     * @param string           $key    Optional key
     *
     * @return $this
     */
    public function addSearch(BaseSearch $search, $key = null)
    {
        if ($key) {
            $this->_searches[$key] = $search;
        } else {
            $this->_searches[] = $search;
        }

        return $this;
    }

    /**
     * @param array|\Webonyx\Elastica3x\Search[] $searches
     *
     * @return $this
     */
    public function addSearches(array $searches)
    {
        foreach ($searches as $key => $search) {
            $this->addSearch($search, $key);
        }

        return $this;
    }

    /**
     * @param array|\Webonyx\Elastica3x\Search[] $searches
     *
     * @return $this
     */
    public function setSearches(array $searches)
    {
        $this->clearSearches();
        $this->addSearches($searches);

        return $this;
    }

    /**
     * @return array|\Webonyx\Elastica3x\Search[]
     */
    public function getSearches()
    {
        return $this->_searches;
    }

    /**
     * @param string $searchType
     *
     * @return $this
     */
    public function setSearchType($searchType)
    {
        $this->_options[BaseSearch::OPTION_SEARCH_TYPE] = $searchType;

        return $this;
    }

    /**
     * @return \Webonyx\Elastica3x\Multi\ResultSet
     */
    public function search()
    {
        $data = $this->_getData();

        $response = $this->getClient()->request(
            '_msearch',
            Request::POST,
            $data,
            $this->_options
        );

        return $this->_builder->buildMultiResultSet($response, $this->getSearches());
    }

    /**
     * @return string
     */
    protected function _getData()
    {
        $data = '';
        foreach ($this->getSearches() as $search) {
            $data .= $this->_getSearchData($search);
        }

        return $data;
    }

    /**
     * @param \Webonyx\Elastica3x\Search $search
     *
     * @return string
     */
    protected function _getSearchData(BaseSearch $search)
    {
        $header = $this->_getSearchDataHeader($search);
        $header = (empty($header)) ? new \stdClass() : $header;
        $query = $search->getQuery();

        $data = JSON::stringify($header)."\n";
        $data .= JSON::stringify($query->toArray())."\n";

        return $data;
    }

    /**
     * @param \Webonyx\Elastica3x\Search $search
     *
     * @return array
     */
    protected function _getSearchDataHeader(BaseSearch $search)
    {
        $header = $search->getOptions();

        if ($search->hasIndices()) {
            $header['index'] = $search->getIndices();
        }

        if ($search->hasTypes()) {
            $header['types'] = $search->getTypes();
        }

        return $header;
    }
}
