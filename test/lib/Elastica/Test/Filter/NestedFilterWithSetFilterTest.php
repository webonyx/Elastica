<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Filter\Nested;
use Webonyx\Elastica3x\Filter\Terms;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Type\Mapping;

class NestedFilterWithSetFilterTest extends BaseTest
{
    protected function _getIndexForTest()
    {
        $index = $this->_createIndex();
        $type = $index->getType('user');

        $type->setMapping(new Mapping(null, [
            'firstname' => ['type' => 'string', 'store' => 'yes'],
            // default is store => no expected
            'lastname' => ['type' => 'string'],
            'hobbies' => [
                'type' => 'nested',
                'include_in_parent' => true,
                'properties' => ['hobby' => ['type' => 'string']],
            ],
        ]));

        $type->addDocuments([
            new Document(1, [
                'firstname' => 'Nicolas',
                'lastname' => 'Ruflin',
                'hobbies' => [
                    ['hobby' => 'opensource'],
                ],
            ]),
            new Document(2, [
                'firstname' => 'Nicolas',
                'lastname' => 'Ippolito',
                'hobbies' => [
                    ['hobby' => 'opensource'],
                    ['hobby' => 'guitar'],
                ],
            ]),
        ]);

        $index->refresh();

        return $index;
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $this->hideDeprecated();
        $filter = new Nested();
        $this->showDeprecated();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobby', ['guitar']);
        $filter->setPath('hobbies');
        $filter->setFilter($query);

        $expectedArray = [
            'nested' => [
                'path' => 'hobbies',
                'filter' => ['terms' => [
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
        $this->hideDeprecated();

        $filter = new Nested();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobbies.hobby', ['guitar']);
        $filter->setPath('hobbies');
        $filter->setFilter($query);

        $client = $this->_getClient();
        $search = new Search($client);
        $index = $this->_getIndexForTest();
        $search->addIndex($index);
        $resultSet = $search->search($filter);

        $this->assertEquals(1, $resultSet->getTotalHits());

        $filter = new Nested();
        $this->assertEquals(['nested' => []], $filter->toArray());
        $query = new Terms();
        $query->setTerms('hobbies.hobby', ['opensource']);
        $filter->setPath('hobbies');
        $filter->setFilter($query);

        $client = $this->_getClient();
        $search = new Search($client);
        $index = $this->_getIndexForTest();
        $search->addIndex($index);
        $resultSet = $search->search($filter);

        $this->showDeprecated();

        $this->assertEquals(2, $resultSet->getTotalHits());
    }
}
