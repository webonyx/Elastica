<?php
namespace Webonyx\Elastica3x\Node;

use Webonyx\Elastica3x\Node as BaseNode;
use Webonyx\Elastica3x\Request;

/**
 * Webonyx\Elastica3x cluster node object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-status.html
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
     * Stats data.
     *
     * @var array stats data
     */
    protected $_data = [];

    /**
     * Node.
     *
     * @var \Webonyx\Elastica3x\Node Node object
     */
    protected $_node;

    /**
     * Create new stats for node.
     *
     * @param \Webonyx\Elastica3x\Node $node Webonyx\Elastica3x node object
     */
    public function __construct(BaseNode $node)
    {
        $this->_node = $node;
        $this->refresh();
    }

    /**
     * Returns all node stats as array based on the arguments.
     *
     * Several arguments can be use
     * get('index', 'test', 'example')
     *
     * @return array Node stats for the given field or null if not found
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
     * Returns all stats data.
     *
     * @return array Data array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Returns node object.
     *
     * @return \Webonyx\Elastica3x\Node Node object
     */
    public function getNode()
    {
        return $this->_node;
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
     * Reloads all nodes information. Has to be called if informations changed.
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function refresh()
    {
        $path = '_nodes/'.$this->getNode()->getName().'/stats';
        $this->_response = $this->getNode()->getClient()->request($path, Request::GET);
        $data = $this->getResponse()->getData();
        $this->_data = reset($data['nodes']);
    }
}
