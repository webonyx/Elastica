<?php
namespace Webonyx\Elastica3x\Index;

use Webonyx\Elastica3x\Index as BaseIndex;
use Webonyx\Elastica3x\Request;

/**
 * Webonyx\Elastica3x index stats object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-stats.html
 */
class Stats
{
    /**
     * Response.
     *
     * @var \Webonyx\Elastica3x\Response Response object
     */
    protected $_response;

    /**
     * Stats info.
     *
     * @var array Stats info
     */
    protected $_data = [];

    /**
     * Index.
     *
     * @var \Webonyx\Elastica3x\Index Index object
     */
    protected $_index;

    /**
     * Construct.
     *
     * @param \Webonyx\Elastica3x\Index $index Index object
     */
    public function __construct(BaseIndex $index)
    {
        $this->_index = $index;
        $this->refresh();
    }

    /**
     * Returns the raw stats info.
     *
     * @return array Stats info
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Returns the entry in the data array based on the params.
     * Various params possible.
     *
     * @return mixed Data array entry or null if not found
     */
    public function get()
    {
        $data = $this->getData();

        foreach (func_get_args() as $arg) {
            if (isset($data[$arg])) {
                $data = $data[$arg];
            } else {
                return;
            }
        }

        return $data;
    }

    /**
     * Returns the index object.
     *
     * @return \Webonyx\Elastica3x\Index Index object
     */
    public function getIndex()
    {
        return $this->_index;
    }

    /**
     * Returns response object.
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Reloads all status data of this object.
     */
    public function refresh()
    {
        $path = '_stats';
        $this->_response = $this->getIndex()->request($path, Request::GET);
        $this->_data = $this->getResponse()->getData();
    }
}
