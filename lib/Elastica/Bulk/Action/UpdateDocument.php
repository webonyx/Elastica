<?php
namespace Webonyx\Elastica3x\Bulk\Action;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Script\AbstractScript;

class UpdateDocument extends IndexDocument
{
    /**
     * @var string
     */
    protected $_opType = self::OP_TYPE_UPDATE;

    /**
     * Set the document for this bulk update action.
     *
     * @param \Webonyx\Elastica3x\Document $document
     *
     * @return $this
     */
    public function setDocument(Document $document)
    {
        parent::setDocument($document);

        $source = ['doc' => $document->getData()];

        if ($document->getDocAsUpsert()) {
            $source['doc_as_upsert'] = true;
        } elseif ($document->hasUpsert()) {
            $upsert = $document->getUpsert()->getData();

            if (!empty($upsert)) {
                $source['upsert'] = $upsert;
            }
        }

        $this->setSource($source);

        return $this;
    }

    /**
     * @param \Webonyx\Elastica3x\Script\AbstractScript $script
     *
     * @return $this
     */
    public function setScript(AbstractScript $script)
    {
        parent::setScript($script);

        // FIXME: can we throw away toArray cast?
        $source = $script->toArray();

        if ($script->hasUpsert()) {
            $upsert = $script->getUpsert()->getData();

            if (!empty($upsert)) {
                $source['upsert'] = $upsert;
            }
        }

        $this->setSource($source);

        return $this;
    }
}
