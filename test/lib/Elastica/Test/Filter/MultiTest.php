<?php
namespace Webonyx\Elastica3x\Test\Filter;

use Webonyx\Elastica3x\Filter\AbstractMulti;
use Webonyx\Elastica3x\Filter\MatchAll;
use Webonyx\Elastica3x\Test\Base;
use Webonyx\Elastica3x\Test\DeprecatedClassBase as BaseTest;

class AbstractMultiTest extends BaseTest
{
    /**
     * @group unit
     */
    public function testDeprecated()
    {
        $reflection = new \ReflectionClass('Webonyx\Elastica3x\Filter\AbstractMulti');
        $this->assertFileDeprecated($reflection->getFileName(), 'Deprecated: Filters are deprecated. Use queries in filter context. See https://www.elastic.co/guide/en/elasticsearch/reference/2.0/query-dsl-filters.html');
    }

    /**
     * @group unit
     */
    public function testConstruct()
    {
        $stub = $this->getStub();

        $this->assertEmpty($stub->getFilters());
    }

    /**
     * @group unit
     */
    public function testAddFilter()
    {
        $stub = $this->getStub();

        $filter = new MatchAll();
        $stub->addFilter($filter);

        $expected = [
            $filter,
        ];

        $this->assertSame($expected, $stub->getFilters());
    }

    /**
     * @group unit
     */
    public function testSetFilters()
    {
        $stub = $this->getStub();

        $filter = new MatchAll();
        $stub->setFilters([$filter]);

        $expected = [
            $filter,
        ];

        $this->assertSame($expected, $stub->getFilters());
    }

    /**
     * @group unit
     */
    public function testToArray()
    {
        $stub = $this->getStub();

        $filter = new MatchAll();
        $stub->addFilter($filter);

        $expected = [
            $stub->getBaseName() => [
                $filter->toArray(),
            ],
        ];

        $this->assertEquals($expected, $stub->toArray());
    }

    /**
     * @group unit
     */
    public function testToArrayWithParam()
    {
        $stub = $this->getStub();

        $stub->setCached(true);

        $filter = new MatchAll();
        $stub->addFilter($filter);

        $expected = [
            $stub->getBaseName() => [
                '_cache' => true,
                'filters' => [
                    $filter->toArray(),
                ],
            ],
        ];

        $this->assertEquals($expected, $stub->toArray());
    }

    private function getStub()
    {
        return $this->getMockForAbstractClass('Webonyx\Elastica3x\Test\Filter\AbstractMultiDebug');
    }
}

Base::hideDeprecated();

class AbstractMultiDebug extends AbstractMulti
{
    public function getBaseName()
    {
        return parent::_getBaseName();
    }
}

Base::showDeprecated();
