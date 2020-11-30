<?php
namespace Webonyx\Elastica3x\Test\Exception;

use Webonyx\Elastica3x\Test\Base as BaseTest;

abstract class AbstractExceptionTest extends BaseTest
{
    protected function _getExceptionClass()
    {
        $reflection = new \ReflectionObject($this);

        // Webonyx\Elastica3x\Test\Exception\RuntimeExceptionTest => Webonyx\Elastica3x\Exception\RuntimeExceptionTest
        $name = preg_replace('/^Webonyx\Elastica3x\\\\Test/', 'Webonyx\Elastica3x', $reflection->getName());

        // Webonyx\Elastica3x\Exception\RuntimeExceptionTest => Webonyx\Elastica3x\Exception\RuntimeException
        $name = preg_replace('/Test$/', '', $name);

        return $name;
    }

    /**
     * @group unit
     */
    public function testInheritance()
    {
        $className = $this->_getExceptionClass();
        $reflection = new \ReflectionClass($className);
        $this->assertTrue($reflection->isSubclassOf('Exception'));
        $this->assertTrue($reflection->implementsInterface('Webonyx\Elastica3x\Exception\ExceptionInterface'));
    }
}
