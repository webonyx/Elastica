<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Query\DisMax;
use Webonyx\Elastica3x\Query\Ids;
use Webonyx\Elastica3x\Query\QueryString;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class DisMaxTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testToArray()
    {
        $query = new DisMax();

        $idsQuery1 = new Ids();
        $idsQuery1->setIds(1);

        $idsQuery2 = new Ids();
        $idsQuery2->setIds(2);

        $idsQuery3 = new Ids();
        $idsQuery3->setIds(3);

        $boost = 1.2;
        $tieBreaker = 2;

        $query->setBoost($boost);
        $query->setTieBreaker($tieBreaker);
        $query->addQuery($idsQuery1);
        $query->addQuery($idsQuery2);
        $query->addQuery($idsQuery3->toArray());

        $expectedArray = [
            'dis_max' => [
                'tie_breaker' => $tieBreaker,
                'boost' => $boost,
                'queries' => [
                    $idsQuery1->toArray(),
                    $idsQuery2->toArray(),
                    $idsQuery3->toArray(),
                ],
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }

    /**
     * @group functional
     */
    public function testQuery()
    {
        $index = $this->_createIndex();
        $type = $index->getType('test');

        $type->addDocuments([
            new Document(1, ['name' => 'Basel-Stadt']),
            new Document(2, ['name' => 'New York']),
            new Document(3, ['name' => 'Baden']),
            new Document(4, ['name' => 'Baden Baden']),
        ]);

        $index->refresh();

        $queryString1 = new QueryString('Bade*');
        $queryString2 = new QueryString('Base*');

        $boost = 1.2;
        $tieBreaker = 2;

        $query = new DisMax();
        $query->setBoost($boost);
        $query->setTieBreaker($tieBreaker);
        $query->addQuery($queryString1);
        $query->addQuery($queryString2);
        $resultSet = $type->search($query);

        $this->assertEquals(3, $resultSet->count());
    }
}
