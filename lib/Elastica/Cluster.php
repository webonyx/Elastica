<?php
namespace Webonyx\Elastica3x;

use Webonyx\Elastica3x\Cluster\Health;
use Webonyx\Elastica3x\Cluster\Settings;
use Webonyx\Elastica3x\Exception\NotImplementedException;

/**
 * Cluster information for elasticsearch.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster.html
 */
class Cluster
{
    /**
     * Client.
     *
     * @var \Webonyx\Elastica3x\Client Client object
     */
    protected $_client;

    /**
     * Cluster state response.
     *
     * @var \Webonyx\Elastica3x\Response
     */
    protected $_response;

    /**
     * Cluster state data.
     *
     * @var array
     */
    protected $_data;

    /**
     * Creates a cluster object.
     *
     * @param \Webonyx\Elastica3x\Client $client Connection client object
     */
    public function __construct(Client $client)
    {
        $this->_client = $client;
        $this->refresh();
    }

    /**
     * Refreshes all cluster information (state).
     */
    public function refresh()
    {
        $path = '_cluster/state';
        $this->_response = $this->_client->request($path, Request::GET);
        $this->_data = $this->getResponse()->getData();
    }

    /**
     * Returns the response object.
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Return list of index names.
     *
     * @return array List of index names
     */
    public function getIndexNames()
    {
        return array_keys($this->_data['metadata']['indices']);
    }

    /**
     * Returns the full state of the cluster.
     *
     * @return array State array
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-state.html
     */
    public function getState()
    {
        return $this->_data;
    }

    /**
     * Returns a list of existing node names.
     *
     * @return array List of node names
     */
    public function getNodeNames()
    {
        $data = $this->getState();
        $nodeNames = [];
        foreach ($data['nodes'] as $node) {
            $nodeNames[] = $node['name'];
        }

        return $nodeNames;
    }

    /**
     * Returns all nodes of the cluster.
     *
     * @return \Webonyx\Elastica3x\Node[]
     */
    public function getNodes()
    {
        $nodes = [];
        $data = $this->getState();

        foreach ($data['nodes'] as $id => $name) {
            $nodes[] = new Node($id, $this->getClient());
        }

        return $nodes;
    }

    /**
     * Returns the client object.
     *
     * @return \Webonyx\Elastica3x\Client Client object
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Returns the cluster information (not implemented yet).
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-info.html
     *
     * @param array $args Additional arguments
     *
     * @throws \Webonyx\Elastica3x\Exception\NotImplementedException
     */
    public function getInfo(array $args)
    {
        throw new NotImplementedException('not implemented yet');
    }

    /**
     * Return Cluster health.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-health.html
     *
     * @return \Webonyx\Elastica3x\Cluster\Health
     */
    public function getHealth()
    {
        return new Health($this->getClient());
    }

    /**
     * Return Cluster settings.
     *
     * @return \Webonyx\Elastica3x\Cluster\Settings
     */
    public function getSettings()
    {
        return new Settings($this->getClient());
    }

    /**
     * Shuts down the complete cluster.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/cluster-nodes-shutdown.html
     *
     * @param string $delay OPTIONAL Seconds to shutdown cluster after (default = 1s)
     *
     * @return \Webonyx\Elastica3x\Response
     */
    public function shutdown($delay = '1s')
    {
        $path = '_shutdown?delay='.$delay;

        return $this->_client->request($path, Request::POST);
    }
}
