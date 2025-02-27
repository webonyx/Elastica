<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Filter\Nested;
use Webonyx\Elastica3x\Query\Terms;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;
use Webonyx\Elastica3x\Type\Mapping;

class NestedTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Nested());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    protected function _getIndexForTest()
    {
        $index = $this->_createIndex('elastica_test_filter_nested');
        $type = $index->getType('user');
        $mapping = new Mapping();
        $mapping->setProperties(
            [
                'firstname' => ['type' => 'string', 'store' => 'yes'],
                // default is store => no expected
                'lastname' => ['type' => 'string'],
                'hobbies' => [
                    'type' => 'nested',
                    'include_in_parent' => true,
                    'properties' => ['hobby' => ['type' => 'string']],
                ],
            ]
        );
        $type->setMapping($mapping);

        $response = $type->addDocuments([
            new Document(1,
                [
                    'firstname' => 'Nicolas',
                    'lastname' => 'Ruflin',
                    'hobbies' => [
                        ['hobby' => 'opensource'],
                    ],
                ]
            ),
            new Document(2,
                [
                    'firstname' => 'Nicolas',
                    'lastname' => 'Ippolito',
                    'hobbies' => [
                        ['hobby' => 'opensource'],
                        ['hobby' => 'guitar'],
                    ],
                ]
            ),
        ]);

        $index->refresh();

        return $index;
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $filter = new Nested();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobby', ['guitar']);
        $filter->setPath('hobbies');
        $filter->setQuery($query);

        $expectedArray = [
            'nested' => [
                'path' => 'hobbies',
                'query' => ['terms' => [
                    'hobby' => ['guitar'],
                ]],
            ],
        ];

        $this->assertEquals($expectedArray, $filter->toArray());
    }

    /**
     * @group functional
     */
    public function testShouldReturnTheRightNumberOfResult()
    {
        $filter = new Nested();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobbies.hobby', ['guitar']);
        $filter->setPath('hobbies');
        $filter->setQuery($query);

        $search = new Search($this->_getClient());
        $search->addIndex($this->_getIndexForTest());
        $resultSet = $search->search($filter);

        $this->assertEquals(1, $resultSet->getTotalHits());

        $filter = new Nested();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobbies.hobby', ['opensource']);
        $filter->setPath('hobbies');
        $filter->setQuery($query);

        $search = new Search($this->_getClient());
        $search->addIndex($this->_getIndexForTest());
        $resultSet = $search->search($filter);
        $this->assertEquals(2, $resultSet->getTotalHits());
    }

    /**
     * @group unit
     */
    public function testSetJoin()
    {
        $filter = new Nested();

        $this->assertTrue($filter->setJoin(true)->getParam('join'));

        $this->assertFalse($filter->setJoin(false)->getParam('join'));

        $returnValue = $filter->setJoin(true);
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\Nested', $returnValue);
    }
}
