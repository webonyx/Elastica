<?php
namespace Webonyx\Elastica3x\Test;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Exception\InvalidException;
use Webonyx\Elastica3x\Filter\Exists;
use Webonyx\Elastica3x\Query;
use Webonyx\Elastica3x\Query\Builder;
use Webonyx\Elastica3x\Query\Term;
use Webonyx\Elastica3x\Query\Text;
use Webonyx\Elastica3x\Script\Script;
use Webonyx\Elastica3x\Script\ScriptFields;
use Webonyx\Elastica3x\Suggest;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Type;

class QueryTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testCreateWithLegacyFilterDeprecated()
    {
        $this->hideDeprecated();
        $existsFilter = new Exists('test');
        $this->showDeprecated();

        $errorsCollector = $this->startCollectErrors();
        Query::create($existsFilter);
        $this->finishCollectErrors();

        $errorsCollector->assertOnlyDeprecatedErrors(
            [
                'Deprecated: Webonyx\Elastica3x\Query::create() passing filter is deprecated. Create query and use setPostFilter with AbstractQuery instead.',
                'Deprecated: Webonyx\Elastica3x\Query::setPostFilter() passing filter as AbstractFilter is deprecated. Pass instance of AbstractQuery instead.',
            ]
        );
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testSetFilterInvalid()
    {
        $query = new Query();
        $query->setFilter($this);
    }

    /**
     * @group unit
     */
    public function testSetFilterWithLegacyFilterDeprecated()
    {
        $this->hideDeprecated();
        $existsFilter = new Exists('test');
        $this->showDeprecated();

        $query = new Query();

        $errorsCollector = $this->startCollectErrors();
        $query->setFilter($existsFilter);
        $this->finishCollectErrors();

        $errorsCollector->assertOnlyDeprecatedErrors(
            [
                'Deprecated: Webonyx\Elastica3x\Query::setFilter() passing filter as AbstractFilter is deprecated. Pass instance of AbstractQuery instead.',
                'Deprecated: Webonyx\Elastica3x\Query::setFilter() is deprecated and will be removed in further Webonyx\Elastica3x releases. Use Webonyx\Elastica3x\Query::setPostFilter() instead.',
                'Deprecated: Webonyx\Elastica3x\Query::setPostFilter() passing filter as AbstractFilter is deprecated. Pass instance of AbstractQuery instead.',
            ]
        );
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     */
    public function testSetPostFilterInvalid()
    {
        $query = new Query();
        $query->setPostFilter($this);
    }

    /**
     * @group unit
     */
    public function testSetPostFilterWithLegacyFilterDeprecated()
    {
        $this->hideDeprecated();
        $existsFilter = new Exists('test');
        $this->showDeprecated();

        $query = new Query();

        $errorsCollector = $this->startCollectErrors();
        $query->setPostFilter($existsFilter);
        $this->finishCollectErrors();

        $errorsCollector->assertOnlyDeprecatedErrors(
            [
                'Deprecated: Webonyx\Elastica3x\Query::setPostFilter() passing filter as AbstractFilter is deprecated. Pass instance of AbstractQuery instead.',
            ]
        );
    }

    /**
     * @group unit
     */
    public function testStringConversion()
    {
        $queryString = '{
            "query" : {
                "filtered" : {
                "filter" : {
                    "range" : {
                    "due" : {
                        "gte" : "2011-07-18 00:00:00",
                        "lt" : "2011-07-25 00:00:00"
                    }
                    }
                },
                "query" : {
                    "text_phrase" : {
                    "title" : "Call back request"
                    }
                }
                }
            },
            "sort" : {
                "due" : {
                "reverse" : true
                }
            },
            "fields" : [
                "created", "assigned_to"
            ]
            }';

        $query = new Builder($queryString);
        $queryArray = $query->toArray();

        $this->assertInternalType('array', $queryArray);

        $this->assertEquals('2011-07-18 00:00:00', $queryArray['query']['filtered']['filter']['range']['due']['gte']);
    }

    /**
     * @group unit
     */
    public function testRawQuery()
    {
        $textQuery = new Term(['title' => 'test']);

        $query1 = Query::create($textQuery);

        $query2 = new Query();
        $query2->setRawQuery(['query' => ['term' => ['title' => 'test']]]);

        $this->assertEquals($query1->toArray(), $query2->toArray());
    }

    /**
     * @group unit
     */
    public function testSuggestShouldNotRemoveOtherParameters()
    {
        $query1 = new Query();
        $query2 = new Query();

        $suggest = new Suggest();
        $suggest->setGlobalText('test');

        $query1->setSize(40);
        $query1->setSuggest($suggest);

        $query2->setSuggest($suggest);
        $query2->setSize(40);

        $this->assertEquals($query1->toArray(), $query2->toArray());
    }

    /**
     * @group unit
     */
    public function testSetSuggestMustReturnQueryInstance()
    {
        $query = new Query();
        $suggest = new Suggest();
        $this->assertInstanceOf('Webonyx\Elastica3x\Query', $query->setSuggest($suggest));
    }

    /**
     * @group unit
     */
    public function testArrayQuery()
    {
        $query = [
            'query' => [
                'text' => [
                    'title' => 'test',
                ],
            ],
        ];

        $query1 = Query::create($query);

        $query2 = new Query();
        $query2->setRawQuery(['query' => ['text' => ['title' => 'test']]]);

        $this->assertEquals($query1->toArray(), $query2->toArray());
    }

    /**
     * @group functional
     */
    public function testSetSort()
    {
        $index = $this->_createIndex();
        $type = $index->getType('test');

        $type->addDocuments([
            new Document(1, ['name' => 'hello world']),
            new Document(2, ['firstname' => 'guschti', 'lastname' => 'ruflin']),
            new Document(3, ['firstname' => 'nicolas', 'lastname' => 'ruflin']),
        ]);

        $queryTerm = new Term();
        $queryTerm->setTerm('lastname', 'ruflin');

        $index->refresh();

        $query = Query::create($queryTerm);

        // ASC order
        $query->setSort([['firstname' => ['order' => 'asc']]]);
        $resultSet = $type->search($query);
        $this->assertEquals(2, $resultSet->count());

        $first = $resultSet->current()->getData();
        $second = $resultSet->next()->getData();

        $this->assertEquals('guschti', $first['firstname']);
        $this->assertEquals('nicolas', $second['firstname']);

        // DESC order
        $query->setSort(['firstname' => ['order' => 'desc']]);
        $resultSet = $type->search($query);
        $this->assertEquals(2, $resultSet->count());

        $first = $resultSet->current()->getData();
        $second = $resultSet->next()->getData();

        $this->assertEquals('nicolas', $first['firstname']);
        $this->assertEquals('guschti', $second['firstname']);
    }

    /**
     * @group unit
     */
    public function testAddSort()
    {
        $query = new Query();
        $sortParam = ['firstname' => ['order' => 'asc']];
        $query->addSort($sortParam);

        $this->assertEquals($query->getParam('sort'), [$sortParam]);
    }

    /**
     * @group unit
     */
    public function testSetRawQuery()
    {
        $query = new Query();

        $params = ['query' => 'test'];
        $query->setRawQuery($params);

        $this->assertEquals($params, $query->toArray());
    }

    /**
     * @group unit
     */
    public function testSetFields()
    {
        $query = new Query();

        $params = ['query' => 'test'];

        $query->setFields(['firstname', 'lastname']);

        $data = $query->toArray();

        $this->assertContains('firstname', $data['fields']);
        $this->assertContains('lastname', $data['fields']);
        $this->assertCount(2, $data['fields']);
    }

    /**
     * @group unit
     */
    public function testGetQuery()
    {
        $query = new Query();

        try {
            $query->getQuery();
            $this->fail('should throw exception because query does not exist');
        } catch (InvalidException $e) {
            $this->assertTrue(true);
        }

        $termQuery = new Term();
        $termQuery->setTerm('text', 'value');
        $query->setQuery($termQuery);

        $this->assertSame($termQuery, $query->getQuery());
    }

    /**
     * @group unit
     */
    public function testSetQueryToArrayCast()
    {
        $query = new Query();
        $termQuery = new Term();
        $termQuery->setTerm('text', 'value');
        $query->setQuery($termQuery);

        $termQuery->setTerm('text', 'another value');

        $anotherQuery = new Query();
        $anotherQuery->setQuery($termQuery);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testNotCloneInnerObjects()
    {
        $query = new Query();
        $termQuery = new Term();
        $termQuery->setTerm('text', 'value');
        $query->setQuery($termQuery);

        $anotherQuery = clone $query;

        $termQuery->setTerm('text', 'another value');

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testSetQueryToArrayChangeQuery()
    {
        $query = new Query();
        $termQuery = new Term();
        $termQuery->setTerm('text', 'value');
        $query->setQuery($termQuery);

        $queryArray = $query->toArray();

        $termQuery = $query->getQuery();
        $termQuery->setTerm('text', 'another value');

        $this->assertNotEquals($queryArray, $query->toArray());
    }

    /**
     * @group unit
     */
    public function testSetScriptFieldsToArrayCast()
    {
        $query = new Query();
        $scriptFields = new ScriptFields();
        $scriptFields->addScript('script',  new Script('script'));

        $query->setScriptFields($scriptFields);

        $scriptFields->addScript('another script',  new Script('another script'));

        $anotherQuery = new Query();
        $anotherQuery->setScriptFields($scriptFields);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testAddScriptFieldsToArrayCast()
    {
        $query = new Query();
        $scriptField = new Script('script');

        $query->addScriptField('script', $scriptField);

        $scriptField->setScript('another script');

        $anotherQuery = new Query();
        $anotherQuery->addScriptField('script', $scriptField);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testAddScriptFieldToExistingScriptFields()
    {
        $script1 = new Script('s1');
        $script2 = new Script('s2');

        // add script1, then add script2
        $query = new Query();
        $scriptFields1 = new ScriptFields();
        $scriptFields1->addScript('script1', $script1);
        $query->setScriptFields($scriptFields1);
        $query->addScriptField('script2', $script2);

        // add script1 and script2 at once
        $anotherQuery = new Query();
        $scriptFields2 = new ScriptFields();
        $scriptFields2->addScript('script1', $script1);
        $scriptFields2->addScript('script2', $script2);
        $anotherQuery->setScriptFields($scriptFields2);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testAddAggregationToArrayCast()
    {
        $query = new Query();
        $aggregation = new \Webonyx\Elastica3x\Aggregation\Terms('text');
        $aggregation->setField('field');

        $query->addAggregation($aggregation);

        $aggregation->setName('another text');

        $anotherQuery = new Query();
        $anotherQuery->addAggregation($aggregation);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testSetSuggestToArrayCast()
    {
        $query = new Query();
        $suggest = new Suggest();
        $suggest->setGlobalText('text');

        $query->setSuggest($suggest);

        $suggest->setGlobalText('another text');

        $anotherQuery = new Query();
        $anotherQuery->setSuggest($suggest);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testSetRescoreToArrayCast()
    {
        $query = new Query();
        $rescore = new \Webonyx\Elastica3x\Rescore\Query();
        $rescore->setQueryWeight(1);

        $query->setRescore($rescore);

        $rescore->setQueryWeight(2);

        $anotherQuery = new Query();
        $anotherQuery->setRescore($rescore);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testSetPostFilterToArrayCast()
    {
        $query = new Query();
        $postFilter = new \Webonyx\Elastica3x\Query\Terms();
        $postFilter->setTerms('key', ['term']);
        $query->setPostFilter($postFilter);

        $postFilter->setTerms('another key', ['another term']);

        $anotherQuery = new Query();
        $anotherQuery->setPostFilter($postFilter);

        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group unit
     */
    public function testLegacySetPostFilterToArrayCast()
    {
        $this->hideDeprecated();

        $query = new Query();
        $postFilter = new \Webonyx\Elastica3x\Filter\Terms();
        $postFilter->setTerms('key', ['term']);
        $query->setPostFilter($postFilter);

        $postFilter->setTerms('another key', ['another term']);

        $anotherQuery = new Query();
        $anotherQuery->setPostFilter($postFilter);

        $this->showDeprecated();
        $this->assertEquals($query->toArray(), $anotherQuery->toArray());
    }

    /**
     * @group functional
     */
    public function testNoSource()
    {
        $index = $this->_createIndex();

        $type = new Type($index, 'user');

        // Adds 1 document to the index
        $doc1 = new Document(1,
            ['username' => 'ruflin', 'test' => ['2', '3', '5']]
        );
        $type->addDocument($doc1);

        // To update index
        $index->refresh();

        $query = Query::create('ruflin');
        $resultSet = $type->search($query);

        // Disable source
        $query->setSource(false);

        $resultSetNoSource = $type->search($query);

        $this->assertEquals(1, $resultSet->count());
        $this->assertEquals(1, $resultSetNoSource->count());

        // Tests if no source is in response except id
        $result = $resultSetNoSource->current();
        $this->assertEquals(1, $result->getId());
        $this->assertEmpty($result->getData());

        // Tests if source is in response except id
        $result = $resultSet->current();
        $this->assertEquals(1, $result->getId());
        $this->assertNotEmpty($result->getData());
    }
}
