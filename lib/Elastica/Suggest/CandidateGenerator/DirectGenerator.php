<?php
namespace Webonyx\Elastica3x\Suggest\CandidateGenerator;

/**
 * Class DirectGenerator.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-suggesters-phrase.html#_direct_generators
 */
class DirectGenerator extends AbstractCandidateGenerator
{
    const SUGGEST_MODE_MISSING = 'missing';
    const SUGGEST_MODE_POPULAR = 'popular';
    const SUGGEST_MODE_ALWAYS = 'always';

    /**
     * @param string $field
     */
    public function __construct($field)
    {
        $this->setField($field);
    }

    /**
     * Set the field name from which to fetch candidate suggestions.
     *
     * @param string $field
     *
     * @return $this
     */
    public function setField($field)
    {
        return $this->setParam('field', $field);
    }

    /**
     * Set the maximum corrections to be returned per suggest text token.
     *
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size)
    {
        return $this->setParam('size', $size);
    }

    /**
     * @param string $mode see SUGGEST_MODE_* constants for options
     *
     * @return $this
     */
    public function setSuggestMode($mode)
    {
        return $this->setParam('suggest_mode', $mode);
    }

    /**
     * @param int $max can only be a value between 1 and 2. Defaults to 2.
     *
     * @return $this
     */
    public function setMaxEdits($max)
    {
        return $this->setParam('max_edits', $max);
    }

    /**
     * @param int $length defaults to 1
     *
     * @return $this
     */
    public function setPrefixLength($length)
    {
        return $this->setParam('prefix_len', $length);
    }

    /**
     * @param int $min defaults to 4
     *
     * @return $this
     */
    public function setMinWordLength($min)
    {
        return $this->setParam('min_word_len', $min);
    }

    /**
     * @param int $max
     *
     * @return $this
     */
    public function setMaxInspections($max)
    {
        return $this->setParam('max_inspections', $max);
    }

    /**
     * @param float $min
     *
     * @return $this
     */
    public function setMinDocFrequency($min)
    {
        return $this->setParam('min_doc_freq', $min);
    }

    /**
     * @param float $max
     *
     * @return $this
     */
    public function setMaxTermFrequency($max)
    {
        return $this->setParam('max_term_freq', $max);
    }

    /**
     * Set an analyzer to be applied to the original token prior to candidate generation.
     *
     * @param string $pre an analyzer
     *
     * @return $this
     */
    public function setPreFilter($pre)
    {
        return $this->setParam('pre_filter', $pre);
    }

    /**
     * Set an analyzer to be applied to generated tokens before they are passed to the phrase scorer.
     *
     * @param string $post
     *
     * @return $this
     */
    public function setPostFilter($post)
    {
        return $this->setParam('post_filter', $post);
    }
}
