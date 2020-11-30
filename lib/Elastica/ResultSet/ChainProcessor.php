<?php
namespace Webonyx\Elastica3x\ResultSet;

use Webonyx\Elastica3x\ResultSet;

/**
 * Allows multiple ProcessorInterface instances to operate on the same
 * ResultSet, calling each in turn.
 */
class ChainProcessor implements ProcessorInterface
{
    /**
     * @var ProcessorInterface[]
     */
    private $processors;

    /**
     * @param ProcessorInterface[] $processors
     */
    public function __construct($processors)
    {
        $this->processors = $processors;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ResultSet $resultSet)
    {
        foreach ($this->processors as $processor) {
            $processor->process($resultSet);
        }
    }
}
