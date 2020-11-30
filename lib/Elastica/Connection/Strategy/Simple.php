<?php
namespace Webonyx\Elastica3x\Connection\Strategy;

use Webonyx\Elastica3x\Exception\ClientException;

/**
 * Description of SimpleStrategy.
 *
 * @author chabior
 */
class Simple implements StrategyInterface
{
    /**
     * @param array|\Webonyx\Elastica3x\Connection[] $connections
     *
     * @throws \Webonyx\Elastica3x\Exception\ClientException
     *
     * @return \Webonyx\Elastica3x\Connection
     */
    public function getConnection($connections)
    {
        foreach ($connections as $connection) {
            if ($connection->isEnabled()) {
                return $connection;
            }
        }

        throw new ClientException('No enabled connection');
    }
}
