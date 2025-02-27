<?php
namespace Webonyx\Elastica3x;

/**
 * Interface for params.
 *
 *
 * @author Evgeniy Sokolov <ewgraf@gmail.com>
 */
interface ArrayableInterface
{
    /**
     * Converts the object to an array.
     *
     * @return array Object as array
     */
    public function toArray();
}
