<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\AbstractGeoShape;
use Webonyx\Elastica3x\Filter\GeoShapePreIndexed;
use Webonyx\Elastica3x\Query\BoolQuery;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class GeoShapePreIndexedTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass(new GeoShapePreIndexed('location', '1', 'type', 'a', 'location'));
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group functional
     */
    public function testGeoProvided()
    {
        $index = $this->_createIndex();
        $indexName = $index->getName();
        $type = $index->getType('type');
        $otherType = $index->getType('other_type');

        // create mapping
        $mapping = new \Webonyx\Elastica3x\Type\Mapping($type, [
            'location' => [
                'type' => 'geo_shape',
            ],
        ]);
        $type->setMapping($mapping);

        // create other type mapping
        $otherMapping = new \Webonyx\Elastica3x\Type\Mapping($type, [
            'location' => [
                'type' => 'geo_shape',
            ],
        ]);
        $otherType->setMapping($otherMapping);

        // add type docs
        $type->addDocument(new \Webonyx\Elastica3x\Document('1', [
            'location' => [
                'type' => 'envelope',
                'coordinates' => [
                    [0.0, 50.0],
                    [50.0, 0.0],
                ],
            ],
        ]));

        // add other type docs
        $otherType->addDocument(new \Webonyx\Elastica3x\Document('2', [
            'location' => [
                'type' => 'envelope',
                'coordinates' => [
                    [25.0, 75.0],
                    [75.0, 25.0],
                ],
            ],
        ]));

        $index->optimize();
        $index->refresh();

        $gsp = new GeoShapePreIndexed(
            'location', '1', 'type', $indexName, 'location'
        );
        $gsp->setRelation(AbstractGeoShape::RELATION_INTERSECT);

        $expected = [
            'geo_shape' => [
                'location' => [
                    'indexed_shape' => [
                        'id' => '1',
                        'type' => 'type',
                        'index' => $indexName,
                        'path' => 'location',
                    ],
                    'relation' => $gsp->getRelation(),
                ],
            ],
        ];

        $this->assertEquals($expected, $gsp->toArray());

        $query = new BoolQuery();
        $query->addFilter($gsp);
        $results = $index->getType('type')->search($query);

        $this->assertEquals(1, $results->count());

        $index->delete();
    }

    /**
     * @group unit
     */
    public function testSetRelation()
    {
        $gsp = new GeoShapePreIndexed('location', '1', 'type', 'indexName', 'location');
        $gsp->setRelation(AbstractGeoShape::RELATION_INTERSECT);
        $this->assertEquals(AbstractGeoShape::RELATION_INTERSECT, $gsp->getRelation());
        $this->assertInstanceOf('Webonyx\Elastica3x\Filter\GeoShapePreIndexed', $gsp->setRelation(AbstractGeoShape::RELATION_INTERSECT));
    }
}
