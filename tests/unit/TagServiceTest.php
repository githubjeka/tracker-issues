<?php

namespace tracker\tests\unit;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\controllers\services\IssueCreator;
use tracker\controllers\requests\IssueRequest;
use tracker\controllers\services\TagDesignator;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @property \tracker\UnitTester tester
 */
class TagServiceTest extends ServiceTest
{
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

        $this->service = new TagDesignator($issue);
        \Yii::$app->user->switchIdentity(User::findOne(['id' => 1]));
    }

    public function testInitService()
    {
        $requestForm = $this->service->getIssueForm();
        $this->assertInstanceOf(IssueRequest::class, $requestForm);
        $this->assertEquals(1, $requestForm->id);
        $this->assertEquals([], $requestForm->tags);
    }
}
