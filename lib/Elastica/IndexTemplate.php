<?php
namespace Webonyx\Elastica3x;

use Webonyx\Elastica3x\Exception\InvalidException;

/**
 * Webonyx\Elastica3x index template object.
 *
 * @author Dmitry Balabka <dmitry.balabka@gmail.com>
 * 
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-templates.html
 */
class IndexTemplate
{
    /**
     * Index template name.
     *
     * @var string Index pattern
     */
    protected $_name;

    /**
     * Client object.
     *
     * @var \Webonyx\Elastica3x\Client Client object
     */
    protected $_client;

    /**
     * Creates a new index template object.
     *
     * @param \Webonyx\Elastica3x\Client $client Client object
     * @param string           $name   Index template name
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function __construct(Client $client, $name)
    {
        $this->_client = $client;

        if (!is_scalar($name)) {
            throw new InvalidException('Index template should be a scalar type');
        }
        $this->_name = (string) $name;
    }

    /**
     * Deletes the index template.
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function delete()
    {
        $response = $this->request(Request::DELETE);

        return $response;
    }

    /**
     * Creates a new index template with the given arguments.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/indices-templates.html
     *
     * @param array $args OPTIONAL Arguments to use
     *
     * @return \Webonyx\Elastica3x\Response
     */
    public function create(array $args = [])
    {
        return $this->request(Request::PUT, $args);
    }

    /**
     * Checks if the given index template is already created.
     *
     * @return bool True if index exists
     */
    public function exists()
    {
        $response = $this->request(Request::HEAD);
        $info = $response->getTransferInfo();

        return (bool) ($info['http_code'] == 200);
    }

    /**
     * Returns the index template name.
     *
     * @return string Index name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Returns index template client.
     *
     * @return \Webonyx\Elastica3x\Client Index client object
     */
    public function getClient()
    {
        return $this->_client;
    }

    /**
     * Makes calls to the elasticsearch server based on this index template name.
     *
     * @param string $method Rest method to use (GET, POST, DELETE, PUT)
     * @param array  $data   OPTIONAL Arguments as array
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    public function request($method, $data = [])
    {
        $path = '/_template/'.$this->getName();

        return $this->getClient()->request($path, $method, $data);
    }
}
