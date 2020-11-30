<?php
namespace lib\Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\ScriptFields as LegacyScriptFields;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class LegacyScriptFieldsTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testParent()
    {
        $this->assertInstanceOf('Webonyx\Elastica3x\Script\ScriptFields', new LegacyScriptFields([]));
    }
}
