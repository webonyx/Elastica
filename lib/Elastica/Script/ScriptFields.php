<?php
namespace Webonyx\Elastica3x\Script;

use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Param;

/**
 * Container for scripts as fields.
 *
 * @author Sebastien Lavoie <github@lavoie.sl>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-script-fields.html
 */
class ScriptFields extends Param
{
    /**
     * @param \Webonyx\Elastica3x\Script\Script[]|array $scripts OPTIONAL
     */
    public function __construct(array $scripts = [])
    {
        if ($scripts) {
            $this->setScripts($scripts);
        }
    }

    /**
     * @param string                          $name   Name of the Script field
     * @param \Webonyx\Elastica3x\Script\AbstractScript $script
     *
     * @throws \Webonyx\Elastica3x\Exception\InvalidException
     *
     * @return $this
     */
    public function addScript($name, AbstractScript $script)
    {
        if (!is_string($name) || !strlen($name)) {
            throw new InvalidException('The name of a Script is required and must be a string');
        }
        $this->setParam($name, $script);

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Script\Script[]|array $scripts Associative array of string => Webonyx\Elastica3x\Script\Script
     *
     * @return $this
     */
    public function setScripts(array $scripts)
    {
        $this->_params = [];
        foreach ($scripts as $name => $script) {
            $this->addScript($name, $script);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_convertArrayable($this->_params);
    }
}
