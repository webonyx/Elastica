<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\ResultSet;
use Webonyx\Elastica3x\ScanAndScroll;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class ScanAndScrollTest extends BaseTest
{
    /**
     * Full foreach test.
     *
     * @group functional
     */
    public function testForeach()
    {
        $scanAndScroll = new ScanAndScroll($this->_prepareSearch(), '1m', 2);
        $docCount = 0;

        /** @var ResultSet $resultSet */
        foreach ($scanAndScroll as $scrollId => $resultSet) {
            $docCount += $resultSet->count();
        }

        /*
         * number of loops and documents per iteration may fluctuate
         * => only test end results
         */
        $this->assertEquals(12, $docCount);
    }

    /**
     * query size revert options.
     *
     * @group functional
     */
    public function testQuerySizeRevert()
    {
        $search = $this->_prepareSearch();
        $search->getQuery()->setSize(9);

        $scanAndScroll = new ScanAndScroll($search);

        $scanAndScroll->rewind();
        $this->assertEquals(9, $search->getQuery()->getParam('size'));

        $scanAndScroll->next();
        $this->assertEquals(9, $search->getQuery()->getParam('size'));
    }

    /**
     * index: 12 docs, 2 shards.
     *
     * @return Search
     */
    private function _prepareSearch()
    {
        $index = $this->_createIndex('', true, 2);
        $index->refresh();

        $docs = [];
        for ($x = 1; $x <= 12; ++$x) {
            $docs[] = new Document($x, ['id' => $x, 'key' => 'value']);
        }

        $type = $index->getType('scanAndScrollTest');
        $type->addDocuments($docs);
        $index->refresh();

        $search = new Search($this->_getClient());
        $search->addIndex($index)->addType($type);

        return $search;
    }
}
