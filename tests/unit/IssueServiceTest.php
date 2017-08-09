<?php

namespace tracker\tests\unit;

use tracker\controllers\requests\IssueRequest;
use tracker\controllers\services\IssueService;


/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class IssueServiceTest extends ServiceTest
{
    /**
     * @Override
     */
    protected function _before()
    {
        $this->service = new IssueService();
    }

    public function testInitService()
    {
        $this->assertInstanceOf(IssueRequest::class, $this->service->getIssueForm());
    }

    public function testLoadAndValidEmptyData()
    {
        $this->assertFalse($this->service->load([]));
        $this->assertFalse($this->service->getIssueForm()->validate());
    }
}
