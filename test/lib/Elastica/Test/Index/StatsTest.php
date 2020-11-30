<?php
namespace Webonyx\Elastica3x\Test\Index;

use Webonyx\Elastica3x\Test\Base as BaseTest;

class StatsTest extends BaseTest
{
    /**
     * @group functional
     */
    public function testGetSettings()
    {
        $indexName = 'test';

        $client = $this->_getClient();
        $index = $client->getIndex($indexName);
        $index->create([], true);
        $stats = $index->getStats();
        $this->assertInstanceOf('Webonyx\Elastica3x\Index\Stats', $stats);

        $this->assertTrue($stats->getResponse()->isOk());
        $this->assertEquals(0, $stats->get('_all', 'indices', 'test', 'primaries', 'docs', 'count'));
    }
}
