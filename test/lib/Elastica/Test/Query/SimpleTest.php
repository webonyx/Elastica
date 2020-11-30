<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Simple;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class SimpleTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $testQuery = ['hello' => ['world'], 'name' => 'ruflin'];
        $query = new Simple($testQuery);

        $this->assertEquals($testQuery, $query->toArray());
    }
}
