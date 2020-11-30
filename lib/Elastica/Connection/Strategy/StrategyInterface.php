<?php
namespace Webonyx\Elastica3x\Connection\Strategy;

/**
 * Description of AbstractStrategy.
 *
 * @author chabior
 */
interface StrategyInterface
{
    /**
     * @param array|\Webonyx\Elastica3x\Connection[] $connections
     *
     * @return \Webonyx\Elastica3x\Connection
     */
    public function getConnection($connections);
}
