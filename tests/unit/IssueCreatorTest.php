<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\controllers\services\IssueCreator;
use tracker\enum\IssueStatusEnum;
use tracker\models\Issue;
use tracker\models\Link;
use tracker\tests\fixtures\LinkFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueCreatorTest extends ServiceTest
{
    /** @var IssueCreator */
    protected $service;

    /**
     * @Override
     */
    protected function _before()
    {
        $this->tester->haveFixtures([
            'issues' => \tracker\tests\fixtures\IssueFixture::class,
            'space' => \humhub\modules\space\tests\codeception\fixtures\SpaceFixture::class,
        ]);

        $this->service = new IssueCreator();

        \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));
    }

    public function testCreateDraftIssue()
    {
        $this->tester->dontSeeRecord(Issue::class, ['id' => 1]);

        $draftIssueModel = $this->createDraft();
        $this->assertEquals(1, $this->service->getIssueForm()->id);
        $this->assertEquals(1, $draftIssueModel->id);
        $this->assertEquals(IssueStatusEnum::TYPE_DRAFT, $draftIssueModel->status);
        $this->tester->seeRecord(Issue::class, ['id' => 1, 'status' => IssueStatusEnum::TYPE_DRAFT]);
    }

    public function testCreateNewIssue()
    {
        $this->createDraft();
        $this->assertFalse($this->service->create());

        $this->service->load(['title' => 'My test issue'], '');

        $requiredAttributesIssue = [
            'id' => 1,
            'title' => 'My test issue',
            'status' => IssueStatusEnum::TYPE_WORK,
        ];

        $this->tester->dontSeeRecord(Issue::class, $requiredAttributesIssue);
        $this->assertInstanceOf(Issue::class, $this->service->create());
        $this->tester->seeRecord(Issue::class, $requiredAttributesIssue);
    }

    public function testStartedTimeForNewIssue()
    {
        $this->createDraft();
        $this->assertFalse($this->service->create());

        $this->service->load(['title' => 'My test issue', 'startedTime' => '10:00', 'startedDate' => '2017-01-31'], '');

        $requiredAttributesIssue = [
            'id' => 1,
            'title' => 'My test issue',
            'status' => IssueStatusEnum::TYPE_WORK,
            'started_at' => '2017-01-31 10:00',
        ];

        $this->tester->dontSeeRecord(Issue::class, $requiredAttributesIssue);
        $this->assertInstanceOf(Issue::class, $this->service->create());
        $this->tester->seeRecord(Issue::class, $requiredAttributesIssue);
    }

    public function testSubtaskIssue()
    {
        $this->tester->haveFixtures(['link' => LinkFixture::class]);

        $this->createDraft();
        $this->service->load(['title' => 'My test issue'], '');
        $issue = $this->service->create();
        $requiredAttributesIssue = ['id' => 1, 'parent_id' => 1, 'child_id' => 2,];

        $this->tester->dontSeeRecord(Link::class, $requiredAttributesIssue);
        $subtaskModel = $this->service->createSubtask($issue, Space::findOne(['id' => 2]));
        $this->assertEquals(2, $subtaskModel->id);
        $this->assertEquals(IssueStatusEnum::TYPE_DRAFT, $subtaskModel->status);
        $this->assertNotEmpty($subtaskModel->started_at);
        $this->tester->seeRecord(Link::class, $requiredAttributesIssue);
    }

    private function createDraft()
    {
        $spaceContent = Space::findOne(['id' => 1]);
        return $this->service->createDraft($spaceContent);
    }
}
