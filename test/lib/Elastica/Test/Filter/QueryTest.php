<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\Query;
use Webonyx\Elastica3x\Query\QueryString;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class QueryTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new Query());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testSimple()
    {
        $query = new QueryString('foo bar');
        $filter = new Query($query);

        $expected = [
            'query' => [
                'query_string' => [
                    'query' => 'foo bar',
                ],
            ],
        ];

        $this->assertEquals($expected, $filter->toArray());
    }

    /**
     * @group unit
     */
    public function testExtended()
    {
        $query = new QueryString('foo bar');
        $filter = new Query($query);
        $filter->setCached(true);

        $expected = [
            'fquery' => [
                'query' => [
                    'query_string' => [
                        'query' => 'foo bar',
                    ],
                ],
                '_cache' => true,
            ],
        ];

        $this->assertEquals($expected, $filter->toArray());
    }
}
