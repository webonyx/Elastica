<?php
namespace Webonyx\Elastica3x\Test\ResultSet;

use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet\DefaultBuilder;
use Webonyx\Elastica3x\Test\Base as BaseTest;

/**
 * @group unit
 */
class BuilderTest extends BaseTest
{
    /**
     * @var DefaultBuilder
     */
    private $builder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = new DefaultBuilder();
    }

    public function testEmptyResponse()
    {
        $response = new Response('');
        $query = new Query();

        $resultSet = $this->builder->buildResultSet($response, $query);

        $this->assertSame($response, $resultSet->getResponse());
        $this->assertSame($query, $resultSet->getQuery());
        $this->assertCount(0, $resultSet->getResults());
    }

    public function testResponse()
    {
        $response = new Response([
            'hits' => [
                'hits' => [
                    ['test' => 1],
                    ['test' => 2],
                    ['test' => 3],
                ],
            ],
        ]);
        $query = new Query();

        $resultSet = $this->builder->buildResultSet($response, $query);

        $this->assertSame($response, $resultSet->getResponse());
        $this->assertSame($query, $resultSet->getQuery());
        $this->assertCount(3, $resultSet->getResults());
    }
}
