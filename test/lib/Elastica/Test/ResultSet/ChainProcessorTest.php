<?php
namespace Webonyx\Elastica3x\Test\Transformer;

use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Response;
use Webonyx\Elastica3x\ResultSet;
use Webonyx\Elastica3x\ResultSet\ChainProcessor;
use Webonyx\Elastica3x\Test\Base as BaseTest;

/**
 * @group unit
 */
class ChainProcessorTest extends BaseTest
{
    public function testProcessor()
    {
        $processor = new ChainProcessor([
            $processor1 = $this->getMock('Webonyx\Elastica3x\\ResultSet\\ProcessorInterface'),
            $processor2 = $this->getMock('Webonyx\Elastica3x\\ResultSet\\ProcessorInterface'),
        ]);
        $resultSet = new ResultSet(new Response(''), new Query(), []);

        $processor1->expects($this->once())
            ->method('process')
            ->with($resultSet);
        $processor2->expects($this->once())
            ->method('process')
            ->with($resultSet);

        $processor->process($resultSet);
    }
}
