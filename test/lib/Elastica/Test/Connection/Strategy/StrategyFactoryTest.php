<?php
namespace Webonyx\Elastica3x\Test\Connection\Strategy;

use Webonyx\Elastica3x\Connection\Strategy\StrategyFactory;
use Webonyx\Elastica3x\Test\Base;

/**
 * Description of StrategyFactoryTest.
 *
 * @author chabior
 */
class StrategyFactoryTest extends Base
{
    /**
     * @group unit
     */
    public function testCreateCallbackStrategy()
    {
        $callback = function ($connections) {
        };

        $strategy = StrategyFactory::create($callback);

        $this->assertInstanceOf('Webonyx\Elastica3x\Connection\Strategy\CallbackStrategy', $strategy);
    }

    /**
     * @group unit
     */
    public function testCreateByName()
    {
        $strategyName = 'Simple';

        $strategy = StrategyFactory::create($strategyName);

        $this->assertInstanceOf('Webonyx\Elastica3x\Connection\Strategy\Simple', $strategy);
    }

    /**
     * @group unit
     */
    public function testCreateByClass()
    {
        $strategy = new EmptyStrategy();

        $this->assertEquals($strategy, StrategyFactory::create($strategy));
    }

    /**
     * @group unit
     */
    public function testCreateByClassName()
    {
        $strategyName = '\\Webonyx\Elastica3x\Test\Connection\Strategy\\EmptyStrategy';

        $strategy = StrategyFactory::create($strategyName);

        $this->assertInstanceOf($strategyName, $strategy);
    }

    /**
     * @group unit
     * @expectedException \InvalidArgumentException
     */
    public function testFailCreate()
    {
        $strategy = new \stdClass();

        StrategyFactory::create($strategy);
    }

    /**
     * @group unit
     */
    public function testNoCollisionWithGlobalNamespace()
    {
        // create collision
        if (!class_exists('Simple')) {
            class_alias('Webonyx\Elastica3x\Util', 'Simple');
        }
        $strategy = StrategyFactory::create('Simple');
        $this->assertInstanceOf('Webonyx\Elastica3x\Connection\Strategy\Simple', $strategy);
    }
}
