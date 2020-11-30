<?php
namespace Webonyx\Elastica3x\Transport;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Param;
use Webonyx\Elastica3x\Request;

/**
 * Webonyx\Elastica3x Abstract Transport object.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
abstract class AbstractTransport extends Param
{
    /**
     * @var \Webonyx\Elastica3x\Connection
     */
    protected $_connection;

    /**
     * Construct transport.
     *
     * @param \Webonyx\Elastica3x\Connection $connection Connection object
     */
    public function __construct(Connection $connection = null)
    {
        if ($connection) {
            $this->setConnection($connection);
        }
    }

    /**
     * @return \Webonyx\Elastica3x\Connection Connection object
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * @param \Webonyx\Elastica3x\Connection $connection Connection object
     *
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->_connection = $connection;

        return $this;
    }

    /**
     * Executes the transport request.
     *
     * @param \Webonyx\Elastica3x\Request $request Request object
     * @param array             $params  Hostname, port, path, ...
     *
     * @return \Webonyx\Elastica3x\Response Response object
     */
    abstract public function exec(Request $request, array $params);

    /**
     * Create a transport.
     *
     * The $transport parameter can be one of the following values:
     *
     * * string: The short name of a transport. For instance "Http"
     * * object: An already instantiated instance of a transport
     * * array: An array with a "type" key which must be set to one of the two options. All other
     *          keys in the array will be set as parameters in the transport instance
     *
     * @param mixed                $transport  A transport definition
     * @param \Webonyx\Elastica3x\Connection $connection A connection instance
     * @param array                $params     Parameters for the transport class
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return AbstractTransport
     */
    public static function create($transport, Connection $connection, array $params = [])
    {
        if (is_array($transport) && isset($transport['type'])) {
            $transportParams = $transport;
            unset($transportParams['type']);

            $params = array_replace($params, $transportParams);
            $transport = $transport['type'];
        }

        if (is_string($transport)) {
            $specialTransports = [
                'httpadapter' => 'HttpAdapter',
                'nulltransport' => 'NullTransport',
            ];

            if (isset($specialTransports[strtolower($transport)])) {
                $transport = $specialTransports[strtolower($transport)];
            } else {
                $transport = ucfirst($transport);
            }
            $classNames = ["Webonyx\Elastica3x\\Transport\\$transport", $transport];
            foreach ($classNames as $className) {
                if (class_exists($className)) {
                    $transport = new $className();
                    break;
                }
            }
        }

        if ($transport instanceof self) {
            $transport->setConnection($connection);

            foreach ($params as $key => $value) {
                $transport->setParam($key, $value);
            }
        } else {
            throw new InvalidException('Invalid transport');
        }

        return $transport;
    }
}
