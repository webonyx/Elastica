<?php
namespace Webonyx\Elastica3x\Query;

/**
 * Geo polygon query.
 *
 * @author Michael Maclean <mgdm@php.net>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-polygon-query.html
 */
class GeoPolygon extends AbstractQuery
{
    /**
     * Key.
     *
     * @var string Key
     */
    protected $_key;

    /**
     * Points making up polygon.
     *
     * @var array Points making up polygon
     */
    protected $_points;

    /**
     * Construct polygon query.
     *
     * @param string $key    Key
     * @param array  $points Points making up polygon
     */
    public function __construct($key, array $points)
    {
        $this->_key = $key;
        $this->_points = $points;
    }

    /**
     * Converts query to array.
     *
     * @see \Webonyx\Elastica3x\Query\AbstractQuery::toArray()
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'geo_polygon' => [
                $this->_key => [
                    'points' => $this->_points,
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     *
     * @return int
     */
    public function count()
    {
        return count($this->_key);
    }
}
