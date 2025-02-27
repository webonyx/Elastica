<?php
namespace Webonyx\Elastica3x\Exception;

/**
 * Not implemented exception.
 *
 * Is thrown if a function or feature is not implemented yet
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 */
class NotImplementedException extends \BadMethodCallException implements ExceptionInterface
{
}
