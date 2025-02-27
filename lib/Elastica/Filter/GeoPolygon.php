<?php
namespace Webonyx\Elastica3x\Filter;

trigger_error('Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html', E_USER_DEPRECATED);

/**
 * Geo polygon filter.
 *
 * @author Michael Maclean <mgdm@php.net>
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-geo-polygon-filter.html
 * @deprecated Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html
 */
class GeoPolygon extends AbstractFilter
{
    /**
     * Key.
     *
     * @var string Key
     */
    protected $_key = '';

    /**
     * Points making up polygon.
     *
     * @var array Points making up polygon
     */
    protected $_points = [];

    /**
     * Construct polygon filter.
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
     * Converts filter to array.
     *
     * @see \Webonyx\Elastica3x\Filter\AbstractFilter::toArray()
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
}
