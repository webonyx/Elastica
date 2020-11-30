<?php
namespace Webonyx\Elastica3x\Test\Query;

use Webonyx\Elastica3x\Document;
use Webonyx\Elastica3x\Index;
use Webonyx\Elastica3x\Query\QueryString;
use Webonyx\Elastica3x\Test\Base as BaseTest;
use Webonyx\Elastica3x\Type;
use Webonyx\Elastica3x\Util;

class EscapeStringTest extends BaseTest
{
    /**
     * @group functional
     */
    public function testSearch()
    {
        $index = $this->_createIndex();
        $index->getSettings()->setNumberOfReplicas(0);

        $type = new Type($index, 'helloworld');

        $doc = new Document(1, [
            'email' => 'test@test.com', 'username' => 'test 7/6 123', 'test' => ['2', '3', '5'], ]
        );
        $type->addDocument($doc);

        // Refresh index
        $index->refresh();

        $queryString = new QueryString(Util::escapeTerm('test 7/6'));
        $resultSet = $type->search($queryString);

        $this->assertEquals(1, $resultSet->count());
    }
}
