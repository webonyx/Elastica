<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Query\Builder;
use Webonyx\Elastica3x\Test\Base as BaseTest;

class BuilderTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $this->hideDeprecated();
        $reflection = new \ReflectionClass(new Builder());
        $this->showDeprecated();

        $this->assertFileDeprecated($reflection->getFileName(), 'This builder is deprecated and will be removed in further Webonyx\Elastica3x releases. Use new Webonyx\Elastica3x\QueryBuilder instead.');
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::factory
     * @covers \Webonyx\Elastica3x\Query\Builder::__construct
     */
    public function testFactory()
    {
        $this->assertInstanceOf(
            'Webonyx\Elastica3x\Query\Builder',
            Builder::factory('some string')
        );
    }

    public function getQueryData()
    {
        return [
            ['allowLeadingWildcard', false, '{"allow_leading_wildcard":"false"}'],
            ['allowLeadingWildcard', true, '{"allow_leading_wildcard":"true"}'],
            ['analyzeWildcard', false, '{"analyze_wildcard":"false"}'],
            ['analyzeWildcard', true, '{"analyze_wildcard":"true"}'],
            ['analyzer', 'someAnalyzer', '{"analyzer":"someAnalyzer"}'],
            ['autoGeneratePhraseQueries', true, '{"auto_generate_phrase_queries":"true"}'],
            ['autoGeneratePhraseQueries', false, '{"auto_generate_phrase_queries":"false"}'],
            ['boost', 2, '{"boost":"2"}'],
            ['boost', 4.2, '{"boost":"4.2"}'],
            ['defaultField', 'fieldName', '{"default_field":"fieldName"}'],
            ['defaultOperator', 'OR', '{"default_operator":"OR"}'],
            ['defaultOperator', 'AND', '{"default_operator":"AND"}'],
            ['enablePositionIncrements', true, '{"enable_position_increments":"true"}'],
            ['enablePositionIncrements', false, '{"enable_position_increments":"false"}'],
            ['explain', true, '{"explain":"true"}'],
            ['explain', false, '{"explain":"false"}'],
            ['from', 42, '{"from":"42"}'],
            ['fuzzyMinSim', 4.2, '{"fuzzy_min_sim":"4.2"}'],
            ['fuzzyPrefixLength', 2, '{"fuzzy_prefix_length":"2"}'],
            ['gt', 10, '{"gt":"10"}'],
            ['gte', 11, '{"gte":"11"}'],
            ['lowercaseExpandedTerms', true, '{"lowercase_expanded_terms":"true"}'],
            ['lt', 10, '{"lt":"10"}'],
            ['lte', 11, '{"lte":"11"}'],
            ['minimumNumberShouldMatch', 21, '{"minimum_number_should_match":"21"}'],
            ['phraseSlop', 6, '{"phrase_slop":"6"}'],
            ['size', 7, '{"size":"7"}'],
            ['tieBreakerMultiplier', 7, '{"tie_breaker_multiplier":"7"}'],
            ['matchAll', 1.1, '{"match_all":{"boost":"1.1"}}'],
            ['fields', ['age', 'sex', 'location'], '{"fields":["age","sex","location"]}'],
        ];
    }

    /**
     * @group unit
     * @dataProvider getQueryData
     * @covers \Webonyx\Elastica3x\Query\Builder::__toString
     * @covers \Webonyx\Elastica3x\Query\Builder::allowLeadingWildcard
     * @covers \Webonyx\Elastica3x\Query\Builder::analyzeWildcard
     * @covers \Webonyx\Elastica3x\Query\Builder::analyzer
     * @covers \Webonyx\Elastica3x\Query\Builder::autoGeneratePhraseQueries
     * @covers \Webonyx\Elastica3x\Query\Builder::boost
     * @covers \Webonyx\Elastica3x\Query\Builder::defaultField
     * @covers \Webonyx\Elastica3x\Query\Builder::defaultOperator
     * @covers \Webonyx\Elastica3x\Query\Builder::enablePositionIncrements
     * @covers \Webonyx\Elastica3x\Query\Builder::explain
     * @covers \Webonyx\Elastica3x\Query\Builder::from
     * @covers \Webonyx\Elastica3x\Query\Builder::fuzzyMinSim
     * @covers \Webonyx\Elastica3x\Query\Builder::fuzzyPrefixLength
     * @covers \Webonyx\Elastica3x\Query\Builder::gt
     * @covers \Webonyx\Elastica3x\Query\Builder::gte
     * @covers \Webonyx\Elastica3x\Query\Builder::lowercaseExpandedTerms
     * @covers \Webonyx\Elastica3x\Query\Builder::lt
     * @covers \Webonyx\Elastica3x\Query\Builder::lte
     * @covers \Webonyx\Elastica3x\Query\Builder::minimumNumberShouldMatch
     * @covers \Webonyx\Elastica3x\Query\Builder::phraseSlop
     * @covers \Webonyx\Elastica3x\Query\Builder::size
     * @covers \Webonyx\Elastica3x\Query\Builder::tieBreakerMultiplier
     * @covers \Webonyx\Elastica3x\Query\Builder::matchAll
     * @covers \Webonyx\Elastica3x\Query\Builder::fields
     */
    public function testAllowLeadingWildcard($method, $argument, $result)
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->$method($argument));
        $this->assertSame($result, (string) $builder);
    }

    public function getQueryTypes()
    {
        return [
            ['bool', 'bool'],
            ['constantScore', 'constant_score'],
            ['disMax', 'dis_max'],
            ['filter', 'filter'],
            ['filteredQuery', 'filtered'],
            ['must', 'must'],
            ['mustNot', 'must_not'],
            ['prefix', 'prefix'],
            ['query', 'query'],
            ['queryString', 'query_string'],
            ['range', 'range'],
            ['should', 'should'],
            ['sort', 'sort'],
            ['term', 'term'],
            ['textPhrase', 'text_phrase'],
            ['wildcard', 'wildcard'],
        ];
    }

    /**
     * @group unit
     * @dataProvider getQueryTypes
     * @covers \Webonyx\Elastica3x\Query\Builder::fieldClose
     * @covers \Webonyx\Elastica3x\Query\Builder::close
     * @covers \Webonyx\Elastica3x\Query\Builder::bool
     * @covers \Webonyx\Elastica3x\Query\Builder::boolClose
     * @covers \Webonyx\Elastica3x\Query\Builder::constantScore
     * @covers \Webonyx\Elastica3x\Query\Builder::constantScoreClose
     * @covers \Webonyx\Elastica3x\Query\Builder::disMax
     * @covers \Webonyx\Elastica3x\Query\Builder::disMaxClose
     * @covers \Webonyx\Elastica3x\Query\Builder::filter
     * @covers \Webonyx\Elastica3x\Query\Builder::filterClose
     * @covers \Webonyx\Elastica3x\Query\Builder::filteredQuery
     * @covers \Webonyx\Elastica3x\Query\Builder::filteredQueryClose
     * @covers \Webonyx\Elastica3x\Query\Builder::must
     * @covers \Webonyx\Elastica3x\Query\Builder::mustClose
     * @covers \Webonyx\Elastica3x\Query\Builder::mustNot
     * @covers \Webonyx\Elastica3x\Query\Builder::mustNotClose
     * @covers \Webonyx\Elastica3x\Query\Builder::prefix
     * @covers \Webonyx\Elastica3x\Query\Builder::prefixClose
     * @covers \Webonyx\Elastica3x\Query\Builder::query
     * @covers \Webonyx\Elastica3x\Query\Builder::queryClose
     * @covers \Webonyx\Elastica3x\Query\Builder::queryString
     * @covers \Webonyx\Elastica3x\Query\Builder::queryStringClose
     * @covers \Webonyx\Elastica3x\Query\Builder::range
     * @covers \Webonyx\Elastica3x\Query\Builder::rangeClose
     * @covers \Webonyx\Elastica3x\Query\Builder::should
     * @covers \Webonyx\Elastica3x\Query\Builder::shouldClose
     * @covers \Webonyx\Elastica3x\Query\Builder::sort
     * @covers \Webonyx\Elastica3x\Query\Builder::sortClose
     * @covers \Webonyx\Elastica3x\Query\Builder::term
     * @covers \Webonyx\Elastica3x\Query\Builder::termClose
     * @covers \Webonyx\Elastica3x\Query\Builder::textPhrase
     * @covers \Webonyx\Elastica3x\Query\Builder::textPhraseClose
     * @covers \Webonyx\Elastica3x\Query\Builder::wildcard
     * @covers \Webonyx\Elastica3x\Query\Builder::wildcardClose
     */
    public function testQueryTypes($method, $queryType)
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->$method()); // open
        $this->assertSame($builder, $builder->{$method.'Close'}()); // close
        $this->assertSame('{"'.$queryType.'":{}}', (string) $builder);
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::fieldOpen
     * @covers \Webonyx\Elastica3x\Query\Builder::fieldClose
     * @covers \Webonyx\Elastica3x\Query\Builder::open
     * @covers \Webonyx\Elastica3x\Query\Builder::close
     */
    public function testFieldOpenAndClose()
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->fieldOpen('someField'));
        $this->assertSame($builder, $builder->fieldClose());
        $this->assertSame('{"someField":{}}', (string) $builder);
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::sortField
     */
    public function testSortField()
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->sortField('name', true));
        $this->assertSame('{"sort":{"name":{"reverse":"true"}}}', (string) $builder);
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::sortFields
     */
    public function testSortFields()
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->sortFields(['field1' => 'asc', 'field2' => 'desc', 'field3' => 'asc']));
        $this->assertSame('{"sort":[{"field1":"asc"},{"field2":"desc"},{"field3":"asc"}]}', (string) $builder);
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::queries
     */
    public function testQueries()
    {
        $queries = [];

        $builder = new Builder();
        $b1 = clone $builder;
        $b2 = clone $builder;

        $queries[] = $b1->term()->field('age', 34)->termClose();
        $queries[] = $b2->term()->field('name', 'christer')->termClose();

        $this->assertSame($builder, $builder->queries($queries));
        $this->assertSame('{"queries":[{"term":{"age":"34"}},{"term":{"name":"christer"}}]}', (string) $builder);
    }

    public function getFieldData()
    {
        return [
            ['name', 'value', '{"name":"value"}'],
            ['name', true, '{"name":"true"}'],
            ['name', false, '{"name":"false"}'],
            ['name', [1, 2, 3], '{"name":["1","2","3"]}'],
            ['name', ['foo', 'bar', 'baz'], '{"name":["foo","bar","baz"]}'],
        ];
    }

    /**
     * @group unit
     * @dataProvider getFieldData
     * @covers \Webonyx\Elastica3x\Query\Builder::field
     */
    public function testField($name, $value, $result)
    {
        $builder = new Builder();
        $this->assertSame($builder, $builder->field($name, $value));
        $this->assertSame($result, (string) $builder);
    }

    /**
     * @group unit
     * @expectedException \Webonyx\Elastica3x\Exception\InvalidException
     * @expectedExceptionMessage The produced query is not a valid json string : "{{}"
     * @covers \Webonyx\Elastica3x\Query\Builder::toArray
     */
    public function testToArrayWithInvalidData()
    {
        $builder = new Builder();
        $builder->open('foo');
        $builder->toArray();
    }

    /**
     * @group unit
     * @covers \Webonyx\Elastica3x\Query\Builder::toArray
     */
    public function testToArray()
    {
        $builder = new Builder();
        $builder->query()->term()->field('category.id', [1, 2, 3])->termClose()->queryClose();
        $expected = [
            'query' => [
                'term' => [
                    'category.id' => [1, 2, 3],
                ],
            ],
        ];
        $this->assertEquals($expected, $builder->toArray());
    }
}
