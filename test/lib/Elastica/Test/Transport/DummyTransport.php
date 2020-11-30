<?php
namespace Webonyx\Elastica3x\Test\Transport;

use Webonyx\Elastica3x\Request;
use Webonyx\Elastica3x\Transport\AbstractTransport;

class DummyTransport extends AbstractTransport
{
    public function exec(Request $request, array $params)
    {
        // empty
    }
}
