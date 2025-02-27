<?php
namespace Webonyx\Elastica3x\Test\Exception;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Exception\ResponseException;

class ResponseExceptionTest extends AbstractExceptionTest
{
    /**
     * @group functional
     */
    public function testCreateExistingIndex()
    {
        $this->_createIndex('woo', true);

        try {
            $this->_createIndex('woo', false);
            $this->fail('Index created when it should fail');
        } catch (ResponseException $ex) {
            $error = $ex->getResponse()->getFullError();
            $this->assertEquals('index_already_exists_exception', $error['type']);
            $this->assertEquals(400, $ex->getElasticsearchException()->getCode());
        }
    }

    /**
     * @group functional
     */
    public function testBadType()
    {
        $index = $this->_createIndex();
        $type = $index->getType('test');

        $type->setMapping([
            'num' => [
                'type' => 'long',
            ],
        ]);

        try {
            $type->addDocument(new Document('', [
                'num' => 'not number at all',
            ]));
            $this->fail('Indexing with wrong type should fail');
        } catch (ResponseException $ex) {
            $error = $ex->getResponse()->getFullError();
            $this->assertEquals('mapper_parsing_exception', $error['type']);
            $this->assertEquals(400, $ex->getElasticsearchException()->getCode());
        }
    }

    /**
     * @group functional
     */
    public function testWhatever()
    {
        $index = $this->_createIndex();
        $index->delete();

        try {
            $index->search();
        } catch (ResponseException $ex) {
            $error = $ex->getResponse()->getFullError();
            $this->assertEquals('index_not_found_exception', $error['type']);
            $this->assertEquals(404, $ex->getElasticsearchException()->getCode());
        }
    }
}
