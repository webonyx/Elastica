<?php
namespace Webonyx\Elastica3x\Test\Connection\Strategy;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Connection\Strategy\RoundRobin;
use Webonyx\Elastica3x\Exception\ConnectionException;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\Test\Base;

/**
 * Description of RoundRobinTest.
 *
 * @author chabior
 */
class RoundRobinTest extends Base
{
    /**
     * @var int Number of seconds to wait before timeout is called. Is set low for tests to have fast tests.
     */
    protected $_timeout = 1;

    /**
     * @group functional
     */
    public function testConnection()
    {
        $config = ['connectionStrategy' => 'RoundRobin'];
        $client = $this->_getClient($config);
        $response = $client->request('/_aliases');
        /* @var $response Response */

        $this->_checkResponse($response);

        $this->_checkStrategy($client);
    }

    /**
     * @group unit
     */
    public function testOldStrategySetted()
    {
        $config = ['roundRobin' => true];
        $client = $this->_getClient($config);

        $this->_checkStrategy($client);
    }

    /**
     * @group functional
     * @expectedException \Webonyx\Elastica3x\Exception\ConnectionException
     */
    public function testFailConnection()
    {
        $config = ['connectionStrategy' => 'RoundRobin', 'host' => '255.255.255.0', 'timeout' => $this->_timeout];
        $client = $this->_getClient($config);

        $this->_checkStrategy($client);

        $client->request('/_aliases');
    }

    /**
     * @group functional
     */
    public function testWithOneFailConnection()
    {
        $connections = [
            new Connection(['host' => '255.255.255.0', 'timeout' => $this->_timeout]),
            new Connection(['host' => $this->_getHost(), 'timeout' => $this->_timeout]),
        ];

        $count = 0;
        $callback = function ($connection, $exception, $client) use (&$count) {
            ++$count;
        };

        $client = $this->_getClient(['connectionStrategy' => 'RoundRobin'], $callback);
        $client->setConnections($connections);

        $response = $client->request('/_aliases');
        /* @var $response Response */

        $this->_checkResponse($response);

        $this->_checkStrategy($client);

        $this->assertLessThan(count($connections), $count);
    }

    /**
     * @group functional
     */
    public function testWithNoValidConnection()
    {
        $connections = [
            new Connection(['host' => '255.255.255.0', 'timeout' => $this->_timeout]),
            new Connection(['host' => '45.45.45.45', 'port' => '80', 'timeout' => $this->_timeout]),
            new Connection(['host' => '10.123.213.123', 'timeout' => $this->_timeout]),
        ];

        $count = 0;
        $client = $this->_getClient(['roundRobin' => true], function () use (&$count) {
            ++$count;
        });

        $client->setConnections($connections);

        try {
            $client->request('/_aliases');
            $this->fail('Should throw exception as no connection valid');
        } catch (ConnectionException $e) {
            $this->assertEquals(count($connections), $count);
            $this->_checkStrategy($client);
        }
    }

    protected function _checkStrategy($client)
    {
        $strategy = $client->getConnectionStrategy();

        $this->assertInstanceOf('Webonyx\Elastica3x\Connection\Strategy\RoundRobin', $strategy);
    }

    protected function _checkResponse($response)
    {
        $this->assertTrue($response->isOk());
    }
}
