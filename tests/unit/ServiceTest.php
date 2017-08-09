<?php

namespace tracker\tests\unit;

use tracker\controllers\services\IssueService;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class ServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var IssueService
     */
    protected $service;

    protected function getRequestFormErrors()
    {
        return 'Error validate form: ' . json_encode($this->service->getIssueForm()->getErrors());
    }
}
