<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Nested;
use Webonyx\Elastica3x\Query\QueryString;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class NestedTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testSetQuery()
    {
        $nested = new Nested();
        $path = 'test1';

        $queryString = new QueryString('test');
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Nested', $nested->setQuery($queryString));
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\Nested', $nested->setPath($path));
        $expected = [
            'nested' => [
                'query' => $queryString->toArray(),
                'path' => $path,
            ],
        ];

        $this->assertEquals($expected, $nested->toArray());
    }
}
