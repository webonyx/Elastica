<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Term;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class TermTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $query = new Term();
        $key = 'name';
        $value = 'nicolas';
        $boost = 2;
        $query->setTerm($key, $value, $boost);

        $data = $query->toArray();

        $this->assertInternalType('array', $data['term']);
        $this->assertInternalType('array', $data['term'][$key]);
        $this->assertEquals($data['term'][$key]['value'], $value);
        $this->assertEquals($data['term'][$key]['boost'], $boost);
    }

    /**
     * @group unit
     */
    public function testDiacriticsValueToArray()
    {
        $query = new Term();
        $key = 'name';
        $value = 'diprè';
        $boost = 2;
        $query->setTerm($key, $value, $boost);

        $data = $query->toArray();

        $this->assertInternalType('array', $data['term']);
        $this->assertInternalType('array', $data['term'][$key]);
        $this->assertEquals($data['term'][$key]['value'], $value);
        $this->assertEquals($data['term'][$key]['boost'], $boost);
    }
}
