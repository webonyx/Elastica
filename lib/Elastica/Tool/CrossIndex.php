<?php
namespace Webonyx\Elastica3x\Tool;

use Webonyx\Elastica3x\Bulk;
use Webonyx\Elastica3x\Index;
use Webonyx\Elastica3x\Query\MatchAll;
use Webonyx\Elastica3x\ScanAndScroll;
use Webonyx\Elastica3x\Search;
use Webonyx\Elastica3x\Type;

/**
 * Functions to move documents and types between indices.
 *
 * @author Manuel Andreo Garcia <andreo.garcia@gmail.com>
 */
class CrossIndex
{
    /**
     * Type option.
     *
     * type: string | string[] | \Webonyx\Elastica3x\Type | \Webonyx\Elastica3x\Type[] | null
     * default: null (means all types)
     */
    const OPTION_TYPE = 'type';

    /**
     * Query option.
     *
     * type: see \Webonyx\Elastica3x\Query::create()
     * default: Webonyx\Elastica3x\Query\MatchAll
     */
    const OPTION_QUERY = 'query';

    /**
     * Expiry time option.
     *
     * type: string (see Webonyx\Elastica3x\ScanAndScroll)
     * default: '1m'
     */
    const OPTION_EXPIRY_TIME = 'expiryTime';

    /**
     * Size per shard option.
     *
     * type: int (see Webonyx\Elastica3x\ScanAndScroll)
     * default: 1000
     */
    const OPTION_SIZE_PER_SHARD = 'sizePerShard';

    /**
     * Reindex documents from an old index to a new index.
     *
     * @link https://www.elastic.co/guide/en/elasticsearch/guide/master/reindex.html
     *
     * @param \Webonyx\Elastica3x\Index $oldIndex
     * @param \Webonyx\Elastica3x\Index $newIndex
     * @param array           $options  keys: CrossIndex::OPTION_* constants
     *
     * @return \Webonyx\Elastica3x\Index The new index object
     */
    public static function reindex(
        Index $oldIndex,
        Index $newIndex,
        array $options = []
    ) {
        // prepare search
        $search = $oldIndex->createSearch();

        $options = array_merge(
            [
                self::OPTION_TYPE => null,
                self::OPTION_QUERY => new MatchAll(),
                self::OPTION_EXPIRY_TIME => '1m',
                self::OPTION_SIZE_PER_SHARD => 1000,
            ],
            $options
        );

        if (isset($options[self::OPTION_TYPE])) {
            $type = $options[self::OPTION_TYPE];
            $search->addTypes(is_array($type) ? $type : [$type]);
        }
        $search->setQuery($options[self::OPTION_QUERY]);

        // search on old index and bulk insert in new index
        $scanAndScroll = new ScanAndScroll(
            $search,
            $options[self::OPTION_EXPIRY_TIME],
            $options[self::OPTION_SIZE_PER_SHARD]
        );
        foreach ($scanAndScroll as $resultSet) {
            $bulk = new Bulk($newIndex->getClient());
            $bulk->setIndex($newIndex);

            foreach ($resultSet as $result) {
                $action = new Bulk\Action();
                $action->setType($result->getType());
                $action->setId($result->getId());
                $action->setSource($result->getData());

                $bulk->addAction($action);
            }

            $bulk->send();
        }

        $newIndex->refresh();

        return $newIndex;
    }

    /**
     * Copies type mappings and documents from an old index to a new index.
     *
     * @see \Webonyx\Elastica3x\Tool\CrossIndex::reindex()
     *
     * @param \Webonyx\Elastica3x\Index $oldIndex
     * @param \Webonyx\Elastica3x\Index $newIndex
     * @param array           $options  keys: CrossIndex::OPTION_* constants
     *
     * @return \Webonyx\Elastica3x\Index The new index object
     */
    public static function copy(
        Index $oldIndex,
        Index $newIndex,
        array $options = []
    ) {
        // normalize types to array of string
        $types = [];
        if (isset($options[self::OPTION_TYPE])) {
            $types = $options[self::OPTION_TYPE];
            $types = is_array($types) ? $types : [$types];

            $types = array_map(
                function ($type) {
                    if ($type instanceof Type) {
                        $type = $type->getName();
                    }

                    return (string) $type;
                },
                $types
            );
        }

        // copy mapping
        foreach ($oldIndex->getMapping() as $type => $mapping) {
            if (!empty($types) && !in_array($type, $types, true)) {
                continue;
            }

            $type = new Type($newIndex, $type);
            $type->setMapping($mapping['properties']);
        }

        // copy documents
        return self::reindex($oldIndex, $newIndex, $options);
    }
}
