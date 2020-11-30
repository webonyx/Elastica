<?php
namespace Webonyx\Elastica3x\Test\Exception;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Exception\PartialShardFailureException;
use Webonyx\Elastica3x\JSON;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\ResultSet;

class PartialShardFailureExceptionTest extends AbstractExceptionTest
{
    /**
     * @group functional
     */
    public function testPartialFailure()
    {
        $this->_checkScriptInlineSetting();
        $client = $this->_getClient();
        $index = $client->getIndex('elastica_partial_failure');
        $index->create([
            'index' => [
                'number_of_shards' => 5,
                'number_of_replicas' => 0,
            ],
        ], true);

        $type = $index->getType('folks');

        $type->addDocument(new Document('', ['name' => 'ruflin']));
        $type->addDocument(new Document('', ['name' => 'bobrik']));
        $type->addDocument(new Document('', ['name' => 'kimchy']));

        $index->refresh();

        $query = Query::create([
            'query' => [
                'filtered' => [
                    'filter' => [
                        'script' => [
                            'script' => 'doc["undefined"] > 8', // compiles, but doesn't work
                        ],
                    ],
                ],
            ],
        ]);

        try {
            $index->search($query);

            $this->fail('PartialShardFailureException should have been thrown');
        } catch (PartialShardFailureException $e) {
            $builder = new ResultSet\DefaultBuilder();
            $resultSet = $builder->buildResultSet($e->getResponse(), $query);
            $this->assertEquals(0, count($resultSet->getResults()));

            $message = JSON::parse($e->getMessage());
            $this->assertTrue(isset($message['failures']), 'Failures are absent');
            $this->assertGreaterThan(0, count($message['failures']), 'Failures are empty');
        }
    }
}
