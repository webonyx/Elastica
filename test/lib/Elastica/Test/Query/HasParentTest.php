<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query\HasParent;
use Webonyx\Elastica3x\Query\Match;
use Webonyx\Elastica3x\Query\MatchAll;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Type\Mapping;

class HasParentTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $q = new MatchAll();

        $type = 'test';

        $query = new HasParent($q, $type);

        $expectedArray = [
            'has_parent' => [
                'query' => $q->toArray(),
                'type' => $type,
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group unit
     */
    public function testSetScope()
    {
        $q = new MatchAll();

        $type = 'test';

        $scope = 'foo';

        $query = new HasParent($q, $type);
        $query->setScope($scope);

        $expectedArray = [
            'has_parent' => [
                'query' => $q->toArray(),
                'type' => $type,
                '_scope' => $scope,
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group functional
     */
    public function testHasParent()
    {
        $index = $this->_createIndex();

        $shopType = $index->getType('shop');
        $productType = $index->getType('product');
        $mapping = new Mapping();
        $mapping->setParent('shop');
        $productType->setMapping($mapping);

        $shopType->addDocuments(
            [
                new Document('zurich', ['brand' => 'google']),
                new Document('london', ['brand' => 'apple']),
            ]
        );

        $doc1 = new Document(1, ['device' => 'chromebook']);
        $doc1->setParent('zurich');

        $doc2 = new Document(2, ['device' => 'macmini']);
        $doc2->setParent('london');

        $productType->addDocument($doc1);
        $productType->addDocument($doc2);

        $index->refresh();

        // All documents
        $parentQuery = new HasParent(new MatchAll(), $shopType->getName());
        $search = new Search($index->getClient());
        $results = $search->search($parentQuery);
        $this->assertEquals(2, $results->count());

        $match = new Match();
        $match->setField('brand', 'google');

        $parentQuery = new HasParent($match, $shopType->getName());
        $search = new Search($index->getClient());
        $results = $search->search($parentQuery);
        $this->assertEquals(1, $results->count());
        $result = $results->current();
        $data = $result->getData();
        $this->assertEquals($data['device'], 'chromebook');
    }
}
