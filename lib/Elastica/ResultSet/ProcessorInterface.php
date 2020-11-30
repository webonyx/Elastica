<?php
namespace Webonyx\Elastica3x\ResultSet;

use Webonyx\Elastica3x\ResultSet;

interface ProcessorInterface
{
    /**
     * Iterates over a ResultSet allowing a processor to iterate over any
     * Results as required.
     *
     * @param ResultSet $resultSet
     */
    public function process(ResultSet $resultSet);
}
