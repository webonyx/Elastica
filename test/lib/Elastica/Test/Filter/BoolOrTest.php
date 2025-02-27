<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Filter\BoolOr;
use Webonyx\Elastica3x\Filter\Ids;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class BoolOrTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new BoolOr());
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use BoolQuery::addShould. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testAddFilter()
    {
        $filter = $this->getMockForAbstractClass('Webonyx\Elastica3x\Filter\AbstractFilter');
        $orFilter = new BoolOr();
        $returnValue = $orFilter->addFilter($filter);
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\BoolOr', $returnValue);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $orFilter = new BoolOr();

        $filter1 = new Ids();
        $filter1->setIds('1');

        $filter2 = new Ids();
        $filter2->setIds('2');

        $orFilter->addFilter($filter1);
        $orFilter->addFilter($filter2);

        $expectedArray = [
            'or' => [
                    $filter1->toArray(),
                    $filter2->toArray(),
                ],
            ];

        $this->assertEquals($expectedArray, $orFilter->toArray());
    }

    /**
     * @group unit
     */
    public function testConstruct()
    {
        $ids1 = new Ids('foo', [1, 2]);
        $ids2 = new Ids('bar', [3, 4]);

        $and1 = new BoolOr([$ids1, $ids2]);

        $and2 = new BoolOr();
        $and2->addFilter($ids1);
        $and2->addFilter($ids2);

        $this->assertEquals($and1->toArray(), $and2->toArray());
    }

    /**
     * @group functional
     */
    public function testOrFilter()
    {
        $index = $this->_createIndex();
        $type = $index->getType('test');

        $doc1 = new Document('', ['categoryId' => 1]);
        $doc2 = new Document('', ['categoryId' => 2]);
        $doc3 = new Document('', ['categoryId' => 3]);

        $type->addDocument($doc1);
        $type->addDocument($doc2);
        $type->addDocument($doc3);

        $index->refresh();

        $boolOr = new \Webonyx\Elastica3x\Filter\BoolOr();
        $boolOr->addFilter(new \Webonyx\Elastica3x\Filter\Term(['categoryId' => '1']));
        $boolOr->addFilter(new \Webonyx\Elastica3x\Filter\Term(['categoryId' => '2']));

        $resultSet = $type->search($boolOr);
        $this->assertEquals(2, $resultSet->count());
    }
}
