<?php
namespace lib\Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\ScriptFile as LegacyScriptFile;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class LegacyScriptFileTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testParent()
    {
        $this->assertInstanceOf('Webonyx\Elastica3x\Script\ScriptFile', new LegacyScriptFile('script_file'));
    }
}
