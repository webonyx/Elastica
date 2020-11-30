<?php
namespace Webonyx\Elastica3x\Bulk\Action;

use Webonyx\Elastica3x\AbstractUpdateAction;
use Webonyx\Elastica3x\Document;

class IndexDocument extends AbstractDocument
{
    /**
     * @var string
     */
    protected $_opType = self::OP_TYPE_INDEX;

    /**
     * @param \Webonyx\Elastica3x\Document $document
     *
     * @return $this
     */
    public function setDocument(Document $document)
    {
        parent::setDocument($document);

        $this->setSource($document->getData());

        return $this;
    }

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
            'ttl',
            'timestamp',
            'retry_on_conflict',
        ];

        $metadata = $action->getOptions($params, true);

        return $metadata;
    }
}
