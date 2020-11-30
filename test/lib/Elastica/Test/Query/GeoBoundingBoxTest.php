<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\GeoBoundingBox;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class GeoBoundingBoxTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testAddCoordinates()
    {
        $key = 'pin.location';
        $coords = ['40.73, -74.1', '40.01, -71.12'];
        $query = new GeoBoundingBox($key, ['1,2', '3,4']);

        $query->addCoordinates($key, $coords);
        $expectedArray = ['top_left' => $coords[0], 'bottom_right' => $coords[1]];
        $this->assertEquals($expectedArray, $query->getParam($key));

        $returnValue = $query->addCoordinates($key, $coords);
        $this->assertInstanceOf('Webonyx\Elastica3x\Query\GeoBoundingBox', $returnValue);
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testAddCoordinatesInvalidException()
    {
        $query = new GeoBoundingBox('foo', []);
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $key = 'pin.location';
        $coords = ['40.73, -74.1', '40.01, -71.12'];
        $query = new GeoBoundingBox($key, $coords);

        $expectedArray = [
            'geo_bounding_box' => [
                $key => [
                    'top_left' => $coords[0],
                    'bottom_right' => $coords[1],
                ],
            ],
        ];

        $this->assertEquals($expectedArray, $query->toArray());
    }
}
