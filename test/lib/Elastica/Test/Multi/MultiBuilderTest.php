<?php
namespace Webonyx\Elastica3x\Test\Multi;

use Webonyx\Elastica3x\Multi\MultiBuilder;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet;
use Webonyx\Elastica3x\ResultSet\BuilderInterface;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Test\Base as BaseTest;

/**
 * @group unit
 */
class MultiBuilderTest extends BaseTest
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var MultiBuilder
     */
    private $multiBuilder;

    protected function setUp()
    {
        parent::setUp();

        $this->builder = $this->getMock('Webonyx\Elastica3x\\ResultSet\\BuilderInterface');
        $this->multiBuilder = new MultiBuilder($this->builder);
    }

    public function testBuildEmptyMultiResultSet()
    {
        $this->builder->expects($this->never())
            ->method('buildResultSet');

        $response = new Response([]);
        $searches = [];

        $result = $this->multiBuilder->buildMultiResultSet($response, $searches);

        $this->assertInstanceOf('Webonyx\Elastica3x\\Multi\\ResultSet', $result);
    }

    public function testBuildMultiResultSet()
    {
        $response = new Response([
            'responses' => [
                [],
                [],
            ],
        ]);
        $searches = [
            $s1 = new Search($this->_getClient(), $this->builder),
            $s2 = new Search($this->_getClient(), $this->builder),
        ];
        $resultSet1 = new ResultSet(new Response([]), $s1->getQuery(), []);
        $resultSet2 = new ResultSet(new Response([]), $s2->getQuery(), []);

        $this->builder->expects($this->exactly(2))
            ->method('buildResultSet')
            ->withConsecutive(
                [$this->isInstanceOf('Webonyx\Elastica3x\\Response'), $s1->getQuery()],
                [$this->isInstanceOf('Webonyx\Elastica3x\\Response'), $s2->getQuery()]
            )
            ->willReturnOnConsecutiveCalls($resultSet1, $resultSet2);

        $result = $this->multiBuilder->buildMultiResultSet($response, $searches);

        $this->assertInstanceOf('Webonyx\Elastica3x\\Multi\\ResultSet', $result);
        $this->assertSame($resultSet1, $result[0]);
        $this->assertSame($resultSet2, $result[1]);
    }
}
