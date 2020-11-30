<?php
namespace Webonyx\Elastica3x\Test\Connection\Strategy;

use Webonyx\Elastica3x\Connection\Strategy\StrategyInterface;

/**
 * Description of EmptyStrategy.
 *
 * @author chabior
 */
class EmptyStrategy implements StrategyInterface
{
    public function getConnection($connections)
    {
        return;
    }
}
