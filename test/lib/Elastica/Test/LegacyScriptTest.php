<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Script as LegacyScript;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class LegacyScriptTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testParent()
    {
        $this->assertInstanceOf('Webonyx\Elastica3x\Script\Script', new LegacyScript('script'));
    }
}
