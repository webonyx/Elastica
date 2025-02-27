<?php
namespace Webonyx\Elastica3x\Query;

trigger_error('Webonyx\Elastica3x\Query\Bool is deprecated. Use BoolQuery instead. From PHP7 bool is reserved word and this class will be removed in further Webonyx\Elastica3x releases', E_USER_DEPRECATED);

/**
 * Bool query.
 *
 * This class is for backward compatibility reason for all php < 7 versions. For PHP 7 and above use BoolQuery as Bool is reserved.
 *
 * @author Nicolas Ruflin <spam@ruflin.com>
 *
 * @deprecated Use BoolQuery instead. From PHP7 bool is reserved word and this class will be removed in further Webonyx\Elastica3x releases
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-bool-query.html
 */
class Bool extends BoolQuery
{
}
