<?php
namespace Webonyx\Elastica3x\Test\ResultSet;

use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet;
use Webonyx\Elastica3x\ResultSet\BuilderInterface;
use Webonyx\Elastica3x\Test\Base as BaseTest;

/**
 * @group unit
 */
class ProcessingBuilderTest extends BaseTest
{
    /**
     * @var ResultSet\ProcessingBuilder
     */
    private $builder;

    /**
     * @var BuilderInterface
     */
    private $innerBuilder;

    /**
     * @var ResultSet\ProcessorInterface
     */
    private $processor;

    protected function setUp()
    {
        parent::setUp();

        $this->innerBuilder = $this->getMock('Webonyx\Elastica3x\\ResultSet\\BuilderInterface');
        $this->processor = $this->getMock('Webonyx\Elastica3x\\ResultSet\\ProcessorInterface');

        $this->builder = new ResultSet\ProcessingBuilder($this->innerBuilder, $this->processor);
    }

    public function testProcessors()
    {
        $response = new Response('');
        $query = new Query();
        $resultSet = new ResultSet($response, $query, []);

        $this->innerBuilder->expects($this->once())
            ->method('buildResultSet')
            ->with($response, $query)
            ->willReturn($resultSet);
        $this->processor->expects($this->once())
            ->method('process')
            ->with($resultSet);

        $this->builder->buildResultSet($response, $query);
    }
}
