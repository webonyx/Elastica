<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Param;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Util;

class ParamTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArrayEmpty()
    {
        $param = new Param();
        $this->assertInstanceOf('Webonyx\Elastica3x\Param', $param);
        $this->assertEquals([$this->_getFilterName($param) => []], $param->toArray());
    }

    /**
     * @group unit
     */
    public function testSetParams()
    {
        $param = new Param();
        $params = ['hello' => 'word', 'nicolas' => 'ruflin'];
        $param->setParams($params);

        $this->assertInstanceOf('Webonyx\Elastica3x\Param', $param);
        $this->assertEquals([$this->_getFilterName($param) => $params], $param->toArray());
    }

    /**
     * @group unit
     */
    public function testSetGetParam()
    {
        $param = new Param();

        $key = 'name';
        $value = 'nicolas ruflin';

        $params = [$key => $value];
        $param->setParam($key, $value);

        $this->assertEquals($params, $param->getParams());
        $this->assertEquals($value, $param->getParam($key));
    }

    /**
     * @group unit
     */
    public function testAddParam()
    {
        $param = new Param();

        $key = 'name';
        $value = 'nicolas ruflin';

        $param->addParam($key, $value);

        $this->assertEquals([$key => [$value]], $param->getParams());
        $this->assertEquals([$value], $param->getParam($key));
    }

    /**
     * @group unit
     */
    public function testAddParam2()
    {
        $param = new Param();

        $key = 'name';
        $value1 = 'nicolas';
        $value2 = 'ruflin';

        $param->addParam($key, $value1);
        $param->addParam($key, $value2);

        $this->assertEquals([$key => [$value1, $value2]], $param->getParams());
        $this->assertEquals([$value1, $value2], $param->getParam($key));
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testGetParamInvalid()
    {
        $param = new Param();

        $param->getParam('notest');
    }

    /**
     * @group unit
     */
    public function testHasParam()
    {
        $param = new Param();

        $key = 'name';
        $value = 'nicolas ruflin';

        $this->assertFalse($param->hasParam($key));

        $param->setParam($key, $value);
        $this->assertTrue($param->hasParam($key));
    }

    protected function _getFilterName($filter)
    {
        return Util::getParamName($filter);
    }
}
