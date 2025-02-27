<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Filter\Prefix;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;
use Webonyx\Elastica3x\Type\Mapping;

class PrefixTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Prefix());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $field = 'name';
        $prefix = 'ruf';

        $filter = new Prefix($field, $prefix);

        $expectedArray = [
            'prefix' => [
                $field => $prefix,
            ],
        ];

        $this->assertequals($expectedArray, $filter->toArray());
    }

    /**
     * @group functional
     */
    public function testDifferentPrefixes()
    {
        $client = $this->_getClient();
        $index = $client->getIndex('test');

        $index->create([], true);
        $type = $index->getType('test');

        $mapping = new Mapping($type, [
                'name' => ['type' => 'string', 'store' => 'no', 'index' => 'not_analyzed'],
            ]
        );
        $type->setMapping($mapping);
        $type->addDocuments([
            new Document(1, ['name' => 'Basel-Stadt']),
            new Document(2, ['name' => 'New York']),
            new Document(3, ['name' => 'Baden']),
            new Document(4, ['name' => 'Baden Baden']),
            new Document(5, ['name' => 'New Orleans']),
        ]);

        $index->refresh();

        $query = new Prefix('name', 'Ba');
        $resultSet = $index->search($query);
        $this->assertEquals(3, $resultSet->count());

        // Lower case should not return a result
        $query = new Prefix('name', 'ba');
        $resultSet = $index->search($query);
        $this->assertEquals(0, $resultSet->count());

        $query = new Prefix('name', 'Baden');
        $resultSet = $index->search($query);
        $this->assertEquals(2, $resultSet->count());

        $query = new Prefix('name', 'Baden B');
        $resultSet = $index->search($query);
        $this->assertEquals(1, $resultSet->count());

        $query = new Prefix('name', 'Baden Bas');
        $resultSet = $index->search($query);
        $this->assertEquals(0, $resultSet->count());
    }

    /**
     * @group functional
     */
    public function testDifferentPrefixesLowercase()
    {
        $client = $this->_getClient();
        $index = $client->getIndex('test');

        $indexParams = [
            'analysis' => [
                'analyzer' => [
                    'lw' => [
                        'type' => 'custom',
                        'tokenizer' => 'keyword',
                        'filter' => ['lowercase'],
                    ],
                ],
            ],
        ];

        $index->create($indexParams, true);
        $type = $index->getType('test');

        $mapping = new Mapping($type, [
                'name' => ['type' => 'string', 'store' => 'no', 'analyzer' => 'lw'],
            ]
        );
        $type->setMapping($mapping);

        $type->addDocuments([
            new Document(1, ['name' => 'Basel-Stadt']),
            new Document(2, ['name' => 'New York']),
            new Document(3, ['name' => 'Baden']),
            new Document(4, ['name' => 'Baden Baden']),
            new Document(5, ['name' => 'New Orleans']),
        ]);

        $index->refresh();

        $query = new Prefix('name', 'ba');
        $resultSet = $index->search($query);
        $this->assertEquals(3, $resultSet->count());

        // Upper case should not return a result
        $query = new Prefix('name', 'Ba');
        $resultSet = $index->search($query);
        $this->assertEquals(0, $resultSet->count());

        $query = new Prefix('name', 'baden');
        $resultSet = $index->search($query);
        $this->assertEquals(2, $resultSet->count());

        $query = new Prefix('name', 'baden b');
        $resultSet = $index->search($query);
        $this->assertEquals(1, $resultSet->count());

        $query = new Prefix('name', 'baden bas');
        $resultSet = $index->search($query);
        $this->assertEquals(0, $resultSet->count());
    }
}
