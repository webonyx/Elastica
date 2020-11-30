<?php
namespace Webonyx\Elastica3x\Test\Exception;

use Webonyx\Elastica3x\Exception\NotImplementedException;

class NotImplementedExceptionTest extends AbstractExceptionTest
{
    /**
     * @group unit
     */
    public function testInstance()
    {
        $code = 4;
        $message = 'Hello world';
        $exception = new NotImplementedException($message, $code);
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
}
