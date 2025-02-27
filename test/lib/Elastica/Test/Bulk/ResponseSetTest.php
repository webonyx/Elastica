<?php
namespace Webonyx\Elastica3x\Test\Bulk;

use Webonyx\Elastica3x\Bulk;
use Webonyx\Elastica3x\Bulk\Action;
use Webonyx\Elastica3x\Exception\Bulk\ResponseException;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class ResponseSetTest extends BaseTest
{
    /**
     * @group unit
     * @dataProvider isOkDataProvider
     */
    public function testIsOk($responseData, $actions, $expected)
    {
        $responseSet = $this->_createResponseSet($responseData, $actions);
        $this->assertEquals($expected, $responseSet->isOk());
    }

    /**
     * @group unit
     */
    public function testGetError()
    {
        list($responseData, $actions) = $this->_getFixture();
        $responseData['items'][1]['index']['ok'] = false;
        $responseData['items'][1]['index']['error'] = 'SomeExceptionMessage';
        $responseData['items'][2]['index']['ok'] = false;
        $responseData['items'][2]['index']['error'] = 'AnotherExceptionMessage';

        try {
            $this->_createResponseSet($responseData, $actions);
            $this->fail('Bulk request should fail');
        } catch (ResponseException $e) {
            $responseSet = $e->getResponseSet();

            $this->assertInstanceOf('Webonyx\Elastica3x\\Bulk\\ResponseSet', $responseSet);

            $this->assertTrue($responseSet->hasError());
            $this->assertEquals('SomeExceptionMessage', $responseSet->getError());
            $this->assertNotEquals('AnotherExceptionMessage', $responseSet->getError());

            $actionExceptions = $e->getActionExceptions();
            $this->assertEquals(2, count($actionExceptions));

            $this->assertInstanceOf('Webonyx\Elastica3x\Exception\Bulk\Response\ActionException', $actionExceptions[0]);
            $this->assertSame($actions[1], $actionExceptions[0]->getAction());
            $this->assertContains('SomeExceptionMessage', $actionExceptions[0]->getMessage());
            $this->assertTrue($actionExceptions[0]->getResponse()->hasError());

            $this->assertInstanceOf('Webonyx\Elastica3x\Exception\Bulk\Response\ActionException', $actionExceptions[1]);
            $this->assertSame($actions[2], $actionExceptions[1]->getAction());
            $this->assertContains('AnotherExceptionMessage', $actionExceptions[1]->getMessage());
            $this->assertTrue($actionExceptions[1]->getResponse()->hasError());
        }
    }

    /**
     * @group unit
     */
    public function testGetBulkResponses()
    {
        list($responseData, $actions) = $this->_getFixture();

        $responseSet = $this->_createResponseSet($responseData, $actions);

        $bulkResponses = $responseSet->getBulkResponses();
        $this->assertInternalType('array', $bulkResponses);
        $this->assertEquals(3, count($bulkResponses));

        foreach ($bulkResponses as $i => $bulkResponse) {
            $this->assertInstanceOf('Webonyx\Elastica3x\\Bulk\\Response', $bulkResponse);
            $bulkResponseData = $bulkResponse->getData();
            $this->assertInternalType('array', $bulkResponseData);
            $this->assertArrayHasKey('_id', $bulkResponseData);
            $this->assertEquals($responseData['items'][$i]['index']['_id'], $bulkResponseData['_id']);
            $this->assertSame($actions[$i], $bulkResponse->getAction());
            $this->assertEquals('index', $bulkResponse->getOpType());
        }
    }

    /**
     * @group unit
     */
    public function testIterator()
    {
        list($responseData, $actions) = $this->_getFixture();

        $responseSet = $this->_createResponseSet($responseData, $actions);

        $this->assertEquals(3, count($responseSet));

        foreach ($responseSet as $i => $bulkResponse) {
            $this->assertInstanceOf('Webonyx\Elastica3x\Bulk\Response', $bulkResponse);
            $bulkResponseData = $bulkResponse->getData();
            $this->assertInternalType('array', $bulkResponseData);
            $this->assertArrayHasKey('_id', $bulkResponseData);
            $this->assertEquals($responseData['items'][$i]['index']['_id'], $bulkResponseData['_id']);
            $this->assertSame($actions[$i], $bulkResponse->getAction());
            $this->assertEquals('index', $bulkResponse->getOpType());
        }

        $this->assertFalse($responseSet->valid());
        $this->assertNotInstanceOf('Webonyx\Elastica3x\Bulk\Response', $responseSet->current());
        $this->assertFalse($responseSet->current());

        $responseSet->next();

        $this->assertFalse($responseSet->valid());
        $this->assertNotInstanceOf('Webonyx\Elastica3x\Bulk\Response', $responseSet->current());
        $this->assertFalse($responseSet->current());

        $responseSet->rewind();

        $this->assertEquals(0, $responseSet->key());
        $this->assertTrue($responseSet->valid());
        $this->assertInstanceOf('Webonyx\Elastica3x\Bulk\Response', $responseSet->current());
    }

    public function isOkDataProvider()
    {
        list($responseData, $actions) = $this->_getFixture();

        $return = [];
        $return[] = [$responseData, $actions, true];
        $responseData['items'][2]['index']['ok'] = false;
        $return[] = [$responseData, $actions, false];

        return $return;
    }

    /**
     * @param array $responseData
     * @param array $actions
     *
     * @return \Webonyx\Elastica3x\Bulk\ResponseSet
     */
    protected function _createResponseSet(array $responseData, array $actions)
    {
        $client = $this->getMock('Webonyx\Elastica3x\\Client', ['request']);

        $client->expects($this->once())
            ->method('request')
            ->withAnyParameters()
            ->will($this->returnValue(new Response($responseData)));

        $bulk = new Bulk($client);
        $bulk->addActions($actions);

        return $bulk->send();
    }

    /**
     * @return array
     */
    protected function _getFixture()
    {
        $responseData = [
            'took' => 5,
            'items' => [
                [
                    'index' => [
                        '_index' => 'index',
                        '_type' => 'type',
                        '_id' => '1',
                        '_version' => 1,
                        'ok' => true,
                    ],
                ],
                [
                    'index' => [
                        '_index' => 'index',
                        '_type' => 'type',
                        '_id' => '2',
                        '_version' => 1,
                        'ok' => true,
                    ],
                ],
                [
                    'index' => [
                        '_index' => 'index',
                        '_type' => 'type',
                        '_id' => '3',
                        '_version' => 1,
                        'ok' => true,
                    ],
                ],
            ],
        ];
        $bulkResponses = [
            new Action(),
            new Action(),
            new Action(),
        ];

        return [$responseData, $bulkResponses];
    }
}
