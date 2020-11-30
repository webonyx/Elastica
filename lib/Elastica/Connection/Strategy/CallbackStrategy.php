<?php
namespace Webonyx\Elastica3x\Connection\Strategy;

use Webonyx\Elastica3x\Exception\InvalidException;

/**
 * Description of CallbackStrategy.
 *
 * @author chabior
 */
class CallbackStrategy implements StrategyInterface
{
    /**
     * @var callable
     */
    protected $_callback;

    /**
     * @param callable $callback
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function __construct($callback)
    {
        if (!self::isValid($callback)) {
            throw new InvalidException(sprintf('Callback should be a callable, %s given!', gettype($callback)));
        }

        $this->_callback = $callback;
    }

    /**
     * @param array|\Webonyx\Elastica3x\Connection[] $connections
     *
     * @return \Webonyx\Elastica3x\Connection
     */
    public function getConnection($connections)
    {
        return call_user_func_array($this->_callback, [$connections]);
    }

    /**
     * @param callable $callback
     *
     * @return bool
     */
    public static function isValid($callback)
    {
        return is_callable($callback);
    }
}
