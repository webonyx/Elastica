<?php
namespace Webonyx\Elastica3x\Connection\Strategy;

/**
 * Description of RoundRobin.
 *
 * @author chabior
 */
class RoundRobin extends Simple
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
        shuffle($connections);

        return parent::getConnection($connections);
    }
}
