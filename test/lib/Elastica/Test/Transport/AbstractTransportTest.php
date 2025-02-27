<?php
namespace Webonyx\Elastica3x\Test\Transport;

use Webonyx\Elastica3x\Connection;
use Webonyx\Elastica3x\Transport\AbstractTransport;
use Webonyx\Elastica3x\Transport\Http;

class AbstractTransportTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return transport configuration and the expected HTTP method.
     *
     * @return array[]
     */
    public function getValidDefinitions()
    {
        $connection = new Connection();

        return [
            ['Http'],
            [['type' => 'Http']],
            [['type' => new Http()]],
            [new Http()],
            ['Webonyx\Elastica3x\Test\Transport\DummyTransport'],
        ];
    }

    /**
     * @group unit
     * @dataProvider getValidDefinitions
     */
    public function testCanCreateTransportInstances($transport)
    {
        $connection = new Connection();
        $params = [];
        $transport = AbstractTransport::create($transport, $connection, $params);
        $this->assertInstanceOf('Webonyx\Elastica3x\Transport\AbstractTransport', $transport);
        $this->assertSame($connection, $transport->getConnection());
    }

    public function getInvalidDefinitions()
    {
        return [
            [['transport' => 'Http']],
            ['InvalidTransport'],
        ];
    }

    /**
     * @group unit
     * @dataProvider getInvalidDefinitions
     * @expectedException Webonyx\Elastica3x\Exception\InvalidException
     * @expectedExceptionMessage Invalid transport
     */
    public function testThrowsExecptionOnInvalidTransportDefinition($transport)
    {
        AbstractTransport::create($transport, new Connection());
    }

    /**
     * @group unit
     */
    public function testCanInjectParamsWhenUsingArray()
    {
        $connection = new Connection();
        $params = [
            'param1' => 'some value',
            'param3' => 'value3',
        ];

        $transport = AbstractTransport::create([
            'type' => 'Http',
            'param1' => 'value1',
            'param2' => 'value2',
        ], $connection, $params);

        $this->assertSame('value1', $transport->getParam('param1'));
        $this->assertSame('value2', $transport->getParam('param2'));
        $this->assertSame('value3', $transport->getParam('param3'));
    }
}
