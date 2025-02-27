<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Filter\BoolNot;
use Webonyx\Elastica3x\Filter\Indices;
use Webonyx\Elastica3x\Filter\Term;
use Webonyx\Elastica3x\Index;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class IndicesTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Indices(new Term(), []));
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $expected = [
            'indices' => [
                'indices' => ['index1', 'index2'],
                'filter' => [
                    'term' => ['tag' => 'wow'],
                ],
                'no_match_filter' => [
                    'term' => ['tag' => 'such filter'],
                ],
            ],
        ];
        $filter = new Indices(new Term(['tag' => 'wow']), ['index1', 'index2']);
        $filter->setNoMatchFilter(new Term(['tag' => 'such filter']));
        $this->assertEquals($expected, $filter->toArray());
    }

    /**
     * @group functional
     */
    public function testIndicesFilter()
    {
        $docs = [
            new Document(1, ['color' => 'blue']),
            new Document(2, ['color' => 'green']),
            new Document(3, ['color' => 'blue']),
            new Document(4, ['color' => 'yellow']),
        ];

        $index1 = $this->_createIndex();
        $index1->addAlias('indices_filter');
        $index1->getType('test')->addDocuments($docs);
        $index1->refresh();

        $index2 = $this->_createIndex();
        $index2->addAlias('indices_filter');
        $index2->getType('test')->addDocuments($docs);
        $index2->refresh();

        $filter = new Indices(new BoolNot(new Term(['color' => 'blue'])), [$index1->getName()]);
        $filter->setNoMatchFilter(new BoolNot(new Term(['color' => 'yellow'])));
        $query = new Query();
        $query->setPostFilter($filter);

        // search over the alias
        $index = $this->_getClient()->getIndex('indices_filter');
        $results = $index->search($query);

        // ensure that the proper docs have been filtered out for each index
        $this->assertEquals(5, $results->count());
        foreach ($results->getResults() as $result) {
            $data = $result->getData();
            $color = $data['color'];
            if ($result->getIndex() === $index1->getName()) {
                $this->assertNotEquals('blue', $color);
            } else {
                $this->assertNotEquals('yellow', $color);
            }
        }
    }

    /**
     * @group unit
     */
    public function testSetIndices()
    {
        $client = $this->_getClient();
        $index1 = $client->getIndex('index1');
        $index2 = $client->getIndex('index2');

        $indices = ['one', 'two'];
        $filter = new Indices(new Term(['color' => 'blue']), $indices);
        $this->assertEquals($indices, $filter->getParam('indices'));

        $indices[] = 'three';
        $filter->setIndices($indices);
        $this->assertEquals($indices, $filter->getParam('indices'));

        $filter->setIndices([$index1, $index2]);
        $expected = [$index1->getName(), $index2->getName()];
        $this->assertEquals($expected, $filter->getParam('indices'));

        $returnValue = $filter->setIndices($indices);
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Indices', $returnValue);
    }

    /**
     * @group unit
     */
    public function testAddIndex()
    {
        $client = $this->_getClient();
        $index = $client->getIndex('someindex');

        $filter = new Indices(new Term(['color' => 'blue']), []);

        $filter->addIndex($index);
        $expected = [$index->getName()];
        $this->assertEquals($expected, $filter->getParam('indices'));

        $filter->addIndex('foo');
        $expected = [$index->getName(), 'foo'];
        $this->assertEquals($expected, $filter->getParam('indices'));

        $returnValue = $filter->addIndex('bar');
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Indices', $returnValue);
    }
}
