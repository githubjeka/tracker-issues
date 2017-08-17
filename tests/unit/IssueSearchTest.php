<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\controllers\services\IssueCreator;
use tracker\controllers\services\IssueEditor;
use tracker\enum\IssueStatusEnum;
use tracker\models\IssueSearch;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class IssueSearchTest extends \Codeception\Test\Unit
{
    /** @var IssueSearch */
    private $searchModel;

    /**
     * @Override
     */
    protected function _before()
    {
        $this->tester->haveFixtures([
            'content' => \tracker\tests\fixtures\ContentFixture::class,
            'issues' => \tracker\tests\fixtures\IssueFixture::class,
            'links' => \tracker\tests\fixtures\LinkFixture::class,
            'space' => \humhub\modules\space\tests\codeception\fixtures\SpaceFixture::class,
        ]);

        \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));

        $spaceContent = Space::findOne(['id' => 3]);
        $issueCreator = new IssueCreator();
        $issueCreator->createDraft($spaceContent);
        $issueCreator->load([
            'title' => 'My test issue1',
            'visibility' => 1,
            'deadlineDate' => '2017-01-06',
            'deadlineTime' => '00:00',
        ], '');
        $issue = $issueCreator->create();
        $issue->updateAttributes(['started_at' => '2017-01-05 00:00']);

        $issueCreator = new IssueCreator();
        $issueCreator->createDraft($spaceContent);
        $issueCreator->load([
            'title' => 'My test issue2',
            'visibility' => 1,
            'deadlineDate' => '2017-01-15',
            'deadlineTime' => '22:00',
        ], '');
        $issue = $issueCreator->create();
        $issue->updateAttributes(['started_at' => '2017-01-10 10:00']);

        $issueCreator = new IssueCreator();
        $issueCreator->createDraft($spaceContent);
        $issueCreator->load(['title' => 'My test issue3', 'visibility' => 1], '');
        $issue = $issueCreator->create();
        $issueEditor = new IssueEditor($issue);
        $issueEditor->load([
            'status' => IssueStatusEnum::TYPE_FINISHED,
            'deadlineDate' => '2017-01-15',
            'deadlineTime' => '22:00',
        ], '');
        $issue = $issueEditor->save();
        $issue->updateAttributes(['started_at' => '2017-01-31 23:59']);

        $issueCreator = new IssueCreator();
        $issue = $issueCreator->createDraft($spaceContent);
        $issue->content->updateAttributes(['visibility' => 1]);
        $issue->updateAttributes(['started_at' => '2017-01-01 00:00']);

        $this->searchModel = new IssueSearch();
    }

    public function testSearch()
    {
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(4, $dataProvider->getTotalCount());
    }

    public function testValidation()
    {
        $this->searchModel->nullIfError = false;
        $this->searchModel->status = 'wrong status issue';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(4, $dataProvider->getTotalCount());

        $this->searchModel->nullIfError = true;
        $this->searchModel->status = 'wrong status issue';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(0, $dataProvider->getTotalCount());
    }

    public function testSearchByStatus()
    {
        $this->searchModel->status = [IssueStatusEnum::TYPE_WORK];
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(2, $dataProvider->getTotalCount());

        $this->searchModel->status = [IssueStatusEnum::TYPE_FINISHED];
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount());

        $this->searchModel->status = [IssueStatusEnum::TYPE_DRAFT];
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount());
    }

    public function testSearchByStartTime()
    {
        $this->searchModel->startStartedDate = '2017-02-01';
        $this->searchModel->endStartedDate = null;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(0, $dataProvider->getTotalCount(), $dataProvider->query->createCommand()->rawSql);

        $this->searchModel->startStartedDate = '2017-01-01';
        $this->searchModel->endStartedDate = null;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(4, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = '2017-01-10';
        $this->searchModel->endStartedDate = null;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(2, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = '2017-01-31';
        $this->searchModel->endStartedDate = null;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = null;
        $this->searchModel->endStartedDate = '2017-01-01';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(0, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = null;
        $this->searchModel->endStartedDate = '2017-01-05';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = null;
        $this->searchModel->endStartedDate = '2017-01-20';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(3, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = null;
        $this->searchModel->endStartedDate = '2017-02-01';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(4, $dataProvider->getTotalCount());

        $this->searchModel->startStartedDate = '2016-12-31';
        $this->searchModel->endStartedDate = '2017-01-11';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(3, $dataProvider->getTotalCount(), $dataProvider->query->createCommand()->rawSql);

        $this->searchModel->startStartedDate = '2017-01-31';
        $this->searchModel->endStartedDate = '2017-02-28';
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount(), $dataProvider->query->createCommand()->rawSql);
    }

    public function testSearchByDeadline()
    {
        $this->searchModel->isConstantly = null;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(4, $dataProvider->getTotalCount());

        $this->searchModel->isConstantly = false;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(3, $dataProvider->getTotalCount());

        $this->searchModel->isConstantly = true;
        $dataProvider = $this->searchModel->search([]);
        $this->assertEquals(1, $dataProvider->getTotalCount());
    }
}

