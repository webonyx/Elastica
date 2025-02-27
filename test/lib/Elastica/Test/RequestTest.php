<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class RequestTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testConstructor()
    {
        $path = 'test';
        $method = Request::POST;
        $query = ['no' => 'params'];
        $data = ['key' => 'value'];

        $request = new Request($path, $method, $data, $query);

        $this->assertEquals($path, $request->getPath());
        $this->assertEquals($method, $request->getMethod());
        $this->assertEquals($query, $request->getQuery());
        $this->assertEquals($data, $request->getData());
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testInvalidConnection()
    {
        $request = new Request('', Request::GET);
        $request->send();
    }

    /**
     * @group functional
     */
    public function testSend()
    {
        $connection = new Connection();
        $connection->setHost($this->_getHost());
        $connection->setPort('9200');

        $request = new Request('_stats', Request::GET, [], [], $connection);

        $response = $request->send();

        $this->assertInstanceOf('Webonyx\Elastica3x\Response', $response);
    }

    /**
     * @group unit
     */
    public function testToString()
    {
        $path = 'test';
        $method = Request::POST;
        $query = ['no' => 'params'];
        $data = ['key' => 'value'];

        $connection = new Connection();
        $connection->setHost($this->_getHost());
        $connection->setPort('9200');

        $request = new Request($path, $method, $data, $query, $connection);

        $data = $request->toArray();

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('method', $data);
        $this->assertArrayHasKey('path', $data);
        $this->assertArrayHasKey('query', $data);
        $this->assertArrayHasKey('data', $data);
        $this->assertArrayHasKey('connection', $data);
        $this->assertEquals($request->getMethod(), $data['method']);
        $this->assertEquals($request->getPath(), $data['path']);
        $this->assertEquals($request->getQuery(), $data['query']);
        $this->assertEquals($request->getData(), $data['data']);
        $this->assertInternalType('array', $data['connection']);
        $this->assertArrayHasKey('host', $data['connection']);
        $this->assertArrayHasKey('port', $data['connection']);
        $this->assertEquals($request->getConnection()->getHost(), $data['connection']['host']);
        $this->assertEquals($request->getConnection()->getPort(), $data['connection']['port']);

        $string = $request->toString();

        $this->assertInternalType('string', $string);

        $string = (string) $request;
        $this->assertInternalType('string', $string);
    }
}
