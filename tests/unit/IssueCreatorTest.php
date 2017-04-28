<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\controllers\IssueCreator;
use tracker\controllers\IssueRequest;
use tracker\enum\IssueStatusEnum;
use tracker\models\Issue;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
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
            'node' => \tracker\tests\fixtures\IssueFixture::class,
            'space' => \humhub\modules\space\tests\codeception\fixtures\SpaceFixture::class,
        ]);

        \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));
    }

    public function testCreateDraft()
    {
        $service = new IssueCreator();
        $this->assertInstanceOf(IssueRequest::class, $service->getIssueForm());

        $this->tester->dontSeeRecord(Issue::class, ['id' => 1]);

        $spaceContent = Space::findOne(['id' => 1]);
        $draftIssueModel = $service->createDraft($spaceContent);

        $this->assertEquals(1, $service->getIssueForm()->id);
        $this->assertEquals(1, $draftIssueModel->id);
        $this->assertEquals(IssueStatusEnum::TYPE_DRAFT, $draftIssueModel->status);
        $this->tester->seeRecord(Issue::class, ['id' => 1, 'status' => IssueStatusEnum::TYPE_DRAFT]);
    }
}
