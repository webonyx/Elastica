<?php
namespace Webonyx\Elastica3x\Bulk\Action;

use Webonyx\Elastica3x\AbstractUpdateAction;

class DeleteDocument extends AbstractDocument
{
    /**
     * @var string
     */
    protected $_opType = self::OP_TYPE_DELETE;

    /**
     * @param \Webonyx\Elastica3x\AbstractUpdateAction $action
     *
     * @return array
     */
    protected function _getMetadata(AbstractUpdateAction $action)
    {
        $params = [
            'index',
            'type',
            'id',
            'version',
            'version_type',
            'routing',
            'parent',
        ];
        $metadata = $action->getOptions($params, true);

        return $metadata;
    }
}
