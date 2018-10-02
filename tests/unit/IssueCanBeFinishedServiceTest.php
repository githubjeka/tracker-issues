<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use tracker\controllers\services\IssueCanBeFinishedService;
use tracker\controllers\services\IssueCreator;
use tracker\models\Assignee;
use tracker\models\Issue;
use tracker\tests\fixtures\AssigneeFixture;

/**
 * @property \tracker\UnitTester tester
 */
class IssueCanBeFinishedServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var Issue
     */
    protected $issue;

    /**
     * @Override
     */
    protected function _before()
    {
        $this->tester->haveFixtures([
            'issues' => \tracker\tests\fixtures\IssueFixture::class,
            'space' => \humhub\modules\space\tests\codeception\fixtures\SpaceFixture::class,
            'assignees' => AssigneeFixture::class,

        ]);

        $spaceContent = Space::findOne(['id' => 1]);
        $creator = new IssueCreator();
        $creator->createDraft($spaceContent);
        $creator->load(['title' => 'My test issue'], '');
        $this->issue = $creator->create();
    }

    public function testService()
    {
        $service = new IssueCanBeFinishedService($this->issue);
        $this->assertFalse($service->result());

        $assignee = new Assignee(['user_id' => 1, 'issue_id' => $this->issue->id, 'finish_mark' => 1]);
        $this->assertTrue($assignee->save());
        $this->issue->refresh();
        $service = new IssueCanBeFinishedService($this->issue);
        $this->assertTrue($service->result());

        $assignee = new Assignee(['user_id' => 1, 'issue_id' => $this->issue->id, 'finish_mark' => 0]);
        $this->assertTrue($assignee->save());
        $this->issue->refresh();
        $service = new IssueCanBeFinishedService($this->issue);
        $this->assertFalse($service->result());

        $assignee->updateAttributes(['finish_mark' => 1]);
        $this->issue->refresh();
        $this->assertTrue($service->result());

        $this->assertEquals(1, $service->updateIssue(true));
        $this->assertEquals(1, $this->issue->can_be_finished);
        $this->assertTrue($service->result());

        $this->assertEquals(1, $service->updateIssue(false));
        $this->assertEquals(0, $this->issue->can_be_finished);
        $this->assertTrue($service->result());

        $this->assertEquals(1, $service->updateIssue(null));
        $this->assertEquals(1, $this->issue->can_be_finished);
        $this->assertTrue($service->result());
    }
}