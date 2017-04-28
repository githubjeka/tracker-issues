<?php
namespace tests\codeception\common;

use tracker\controllers\IssueCreator;
use tracker\controllers\IssueRequest;
use tracker\tests\fixtures\IssueFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class IssueCreatorTest extends \Codeception\Test\Unit
{
    /**
     * @Override
     */
    protected function _before()
    {
        $this->tester->haveFixtures([
            'node' => [
                'class' => IssueFixture::class,
            ],
        ]);
    }

    public function testCreate()
    {
        $service = new IssueCreator();
        $this->assertInstanceOf(IssueRequest::class, $service->getIssueForm());
    }
}
