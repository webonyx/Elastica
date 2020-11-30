<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class ConnectionTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testEmptyConstructor()
    {
        $connection = new Connection();
        $this->assertEquals(Connection::DEFAULT_HOST, $connection->getHost());
        $this->assertEquals(Connection::DEFAULT_PORT, $connection->getPort());
        $this->assertEquals(Connection::DEFAULT_TRANSPORT, $connection->getTransport());
        $this->assertInstanceOf('Webonyx\Elastica3x\Transport\AbstractTransport', $connection->getTransportObject());
        $this->assertEquals(Connection::TIMEOUT, $connection->getTimeout());
        $this->assertEquals(Connection::CONNECT_TIMEOUT, $connection->getConnectTimeout());
        $this->assertEquals([], $connection->getConfig());
        $this->assertTrue($connection->isEnabled());
    }

    /**
     * @group unit
     */
    public function testEnabledDisable()
    {
        $connection = new Connection();
        $this->assertTrue($connection->isEnabled());
        $connection->setEnabled(false);
        $this->assertFalse($connection->isEnabled());
        $connection->setEnabled(true);
        $this->assertTrue($connection->isEnabled());
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\ConnectionException
     */
    public function testInvalidConnection()
    {
        $connection = new Connection(['port' => 9999]);

        $request = new Request('_stats', Request::GET);
        $request->setConnection($connection);

        // Throws exception because no valid connection
        $request->send();
    }

    /**
     * @group unit
     */
    public function testCreate()
    {
        $connection = Connection::create();
        $this->assertInstanceOf('Webonyx\Elastica3x\Connection', $connection);

        $connection = Connection::create([]);
        $this->assertInstanceOf('Webonyx\Elastica3x\Connection', $connection);

        $port = 9999;
        $connection = Connection::create(['port' => $port]);
        $this->assertInstanceOf('Webonyx\Elastica3x\Connection', $connection);
        $this->assertEquals($port, $connection->getPort());
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testCreateInvalid()
    {
        Connection::create('test');
    }

    /**
     * @group unit
     */
    public function testGetConfig()
    {
        $url = 'test';
        $connection = new Connection(['config' => ['url' => $url]]);
        $this->assertTrue($connection->hasConfig('url'));
        $this->assertEquals($url, $connection->getConfig('url'));
    }

    /**
     * @group unit
     */
    public function testGetConfigWithArrayUsedForTransport()
    {
        $connection = new Connection(['transport' => ['type' => 'Http']]);
        $this->assertInstanceOf('Webonyx\Elastica3x\Transport\Http', $connection->getTransportObject());
    }

    /**
     * @group unit
     * @expectedException Webonyx\Elastica3x\Exception\InvalidException
     * @expectedExceptionMessage Invalid transport
     */
    public function testGetInvalidConfigWithArrayUsedForTransport()
    {
        $connection = new Connection(['transport' => ['type' => 'invalidtransport']]);
        $connection->getTransportObject();
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testGetConfigInvalidValue()
    {
        $connection = new Connection();
        $connection->getConfig('url');
    }

    /**
     * @group unit
     */
    public function testCompression()
    {
        $connection = new Connection();

        $this->assertFalse($connection->hasCompression());
        $connection->setCompression(true);
        $this->assertTrue($connection->hasCompression());
    }

    /**
     * @group unit
     */
    public function testCompressionDefaultWithClient()
    {
        $client = new \Webonyx\Elastica3x\Client();
        $connection = $client->getConnection();
        $this->assertFalse($connection->hasCompression());
    }

    /**
     * @group unit
     */
    public function testCompressionEnabledWithClient()
    {
        $client = new \Webonyx\Elastica3x\Client(['connections' => [['compression' => true]]]);
        $connection = $client->getConnection();

        $this->assertTrue($connection->hasCompression());
    }

    /**
     * @group unit
     */
    public function testUsernameFromClient()
    {
        $username = 'foo';
        $client = new \Webonyx\Elastica3x\Client(['username' => $username]);

        $this->assertEquals($username, $client->getConnection()->getUsername('username'));
    }

    /**
     * @group unit
     */
    public function testPasswordFromClient()
    {
        $password = 'bar';
        $client = new \Webonyx\Elastica3x\Client(['password' => $password]);

        $this->assertEquals($password, $client->getConnection()->getPassword('password'));
    }
}
