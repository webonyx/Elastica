<?php
namespace Webonyx\Elastica3x\Test\Tool;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Test\Base;
use Webonyx\Elastica3x\Tool\CrossIndex;
use Webonyx\Elastica3x\Type;

class CrossIndexTest extends Base
{
    /**
     * Test default reindex.
     *
     * @group functional
     */
    public function testReindex()
    {
        $oldIndex = $this->_createIndex(null, true, 2);
        $this->_addDocs($oldIndex->getType('crossIndexTest'), 10);

        $newIndex = $this->_createIndex(null, true, 2);

        $this->assertInstanceOf(
            'Webonyx\Elastica3x\Index',
            CrossIndex::reindex($oldIndex, $newIndex)
        );

        $this->assertEquals(10, $newIndex->count());

        $oldResult = [];

        foreach ($oldIndex->search()->getResults() as $result) {
            $oldResult[] = $result->getData();
        }

        $newResult = [];

        foreach ($newIndex->search()->getResults() as $result) {
            $newResult[] = $result->getData();
        }

        $this->assertEquals($oldResult, $newResult);
    }

    /**
     * Test reindex type option.
     *
     * @group functional
     */
    public function testReindexTypeOption()
    {
        $oldIndex = $this->_createIndex('', true, 2);
        $type1 = $oldIndex->getType('crossIndexTest_1');
        $type2 = $oldIndex->getType('crossIndexTest_2');

        $docs1 = $this->_addDocs($type1, 10);
        $docs2 = $this->_addDocs($type2, 10);

        $newIndex = $this->_createIndex(null, true, 2);

        // \Webonyx\Elastica3x\Type
        CrossIndex::reindex($oldIndex, $newIndex, [
            CrossIndex::OPTION_TYPE => $type1,
        ]);
        $this->assertEquals(10, $newIndex->count());
        $newIndex->deleteDocuments($docs1);

        // string
        CrossIndex::reindex($oldIndex, $newIndex, [
            CrossIndex::OPTION_TYPE => 'crossIndexTest_2',
        ]);
        $this->assertEquals(10, $newIndex->count());
        $newIndex->deleteDocuments($docs2);

        // array
        CrossIndex::reindex($oldIndex, $newIndex, [
            CrossIndex::OPTION_TYPE => [
                'crossIndexTest_1',
                $type2,
            ],
        ]);
        $this->assertEquals(20, $newIndex->count());
    }

    /**
     * Test default copy.
     *
     * @group functional
     */
    public function testCopy()
    {
        $oldIndex = $this->_createIndex(null, true, 2);
        $newIndex = $this->_createIndex(null, true, 2);

        $oldType = $oldIndex->getType('copy_test');
        $oldMapping = [
            'name' => [
                'type' => 'string',
                'store' => true,
            ],
        ];
        $oldType->setMapping($oldMapping);
        $docs = $this->_addDocs($oldType, 10);

        // mapping
        $this->assertInstanceOf(
            'Webonyx\Elastica3x\Index',
            CrossIndex::copy($oldIndex, $newIndex)
        );

        $newMapping = $newIndex->getType('copy_test')->getMapping();
        if (!isset($newMapping['copy_test']['properties']['name'])) {
            $this->fail('could not request new mapping');
        }

        $this->assertEquals(
            $oldMapping['name'],
            $newMapping['copy_test']['properties']['name']
        );

        // document copy
        $this->assertEquals(10, $newIndex->count());
        $newIndex->deleteDocuments($docs);

        // ignore mapping
        $ignoredType = $oldIndex->getType('copy_test_1');
        $this->_addDocs($ignoredType, 10);

        CrossIndex::copy($oldIndex, $newIndex, [
            CrossIndex::OPTION_TYPE => $oldType,
        ]);

        $this->assertFalse($newIndex->getType($ignoredType->getName())->exists());
        $this->assertEquals(10, $newIndex->count());
    }

    /**
     * @param Type $type
     * @param int  $docs
     *
     * @return array
     */
    private function _addDocs(Type $type, $docs)
    {
        $insert = [];
        for ($i = 1; $i <= $docs; ++$i) {
            $insert[] = new Document($i, ['id' => $i, 'key' => 'value']);
        }

        $type->addDocuments($insert);
        $type->getIndex()->refresh();

        return $insert;
    }
}
