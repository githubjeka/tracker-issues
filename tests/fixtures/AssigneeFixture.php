<?php

namespace tracker\tests\fixtures;

use humhub\modules\user\tests\codeception\fixtures\UserFixture;
use tracker\models\Assignee;
use yii\test\ActiveFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class AssigneeFixture extends ActiveFixture
{
    public $modelClass = Assignee::class;
    public $depends = [
        UserFixture::class,
        IssueFixture::class,
    ];
}
