<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\controllers\services\IssueCreator;
use tracker\controllers\services\IssueEditor;
use tracker\enum\IssuePriorityEnum;
use tracker\enum\IssueStatusEnum;
use tracker\models\Issue;
use yii\helpers\ArrayHelper;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueEditorTest extends ServiceTest
{
    /** @var IssueEditor */
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

        $spaceContent = Space::findOne(['id' => 1]);
        $creator = new IssueCreator();
        $creator->createDraft($spaceContent);
        $creator->load(['title' => 'My test issue'], '');
        $issue = $creator->create();

        $this->service = new IssueEditor($issue);
        \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));
    }

    public function testInitEditIssue()
    {
        $this->assertTrue($this->service->getIssueForm()->validate(), $this->getRequestFormErrors());
        $this->tester->seeRecord(Issue::class, $this->getOldIssueAttributes());
        $issue = Issue::findOne(1);
        $this->assertNotNull($issue->started_at);
        $this->assertEmpty($issue->assignees);
        $this->assertEmpty($issue->tags);
    }

    public function testEditTitleIssue()
    {
        $attr = ['title' => 'New title'];
        $this->changeAttribute($attr, ArrayHelper::merge($this->getOldIssueAttributes(), $attr));
    }

    public function testEditDescriptionIssue()
    {
        $attr = ['description' => '`Description` and some text'];
        $this->changeAttribute($attr, ArrayHelper::merge($this->getOldIssueAttributes(), $attr));
    }

    public function testEditStartedAtIssue()
    {
        $this->changeAttribute(
            ['startedDate' => '2017-01-20', 'startedTime' => '14:50'],
            ArrayHelper::merge($this->getOldIssueAttributes(), ['started_at' => '2017-01-20 14:50'])
        );

        $this->changeAttribute(
            ['startedDate' => null, 'startedTime' => '23:50'],
            ArrayHelper::merge($this->getOldIssueAttributes(), ['started_at' => '2017-01-20 14:50']),
            false
        );
    }

    public function testEditDeadlineIssue()
    {
        $this->changeAttribute(
            ['deadlineDate' => '2017-04-29', 'deadlineTime' => '14:10'],
            ArrayHelper::merge($this->getOldIssueAttributes(), ['deadline' => '2017-04-29 14:10'])
        );
    }

    public function testEditPriorityIssue()
    {
        foreach (array_keys(IssuePriorityEnum::getList()) as $pr) {
            $attr = ['priority' => $pr];
            $this->changeAttribute($attr, ArrayHelper::merge($this->getOldIssueAttributes(), $attr));
        }
    }

    public function testEditStatusToDraftIssue()
    {
        $this->changeAttribute(['status' => IssueStatusEnum::TYPE_DRAFT], $this->getOldIssueAttributes(), false);
    }

    public function testEditStatusToFinishIssue()
    {
        $attr = ArrayHelper::merge($this->getOldIssueAttributes(), ['status' => IssueStatusEnum::TYPE_FINISHED]);
        unset($attr['finished_at']);
        $this->changeAttribute(['status' => IssueStatusEnum::TYPE_FINISHED], $attr);
        $issue = Issue::findOne(1);
        $this->assertNotEmpty($issue->finished_at);
    }

    private function changeAttribute(array $attrToSave, array $expectedNewAttribute, $checkDontSeeRecord = true)
    {
        if ($checkDontSeeRecord) {
            $this->tester->dontSeeRecord(Issue::class, $expectedNewAttribute);
        }
        $this->service->load($attrToSave, '');
        $this->assertNotFalse($this->service->save(), $this->getRequestFormErrors());
        $this->tester->seeRecord(Issue::class, $expectedNewAttribute);
    }

    private function getOldIssueAttributes()
    {
        return [
            'id' => 1,
            'title' => 'My test issue',
            'status' => IssueStatusEnum::TYPE_WORK,
            'description' => null,
            'deadline' => null,
            'priority' => IssuePriorityEnum::TYPE_NORMAL,
            'finished_at' => null,
        ];
    }
}
