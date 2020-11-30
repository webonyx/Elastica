<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Prefix;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class PrefixTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $query = new Prefix();
        $key = 'name';
        $value = 'ni';
        $boost = 2;
        $query->setPrefix($key, $value, $boost);

        $data = $query->toArray();

        $this->assertInternalType('array', $data['prefix']);
        $this->assertInternalType('array', $data['prefix'][$key]);
        $this->assertEquals($data['prefix'][$key]['value'], $value);
        $this->assertEquals($data['prefix'][$key]['boost'], $boost);
    }
}
