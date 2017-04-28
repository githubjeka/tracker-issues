<?php

namespace tracker\tests\fixtures;

use tracker\models\Issue;
use yii\test\ActiveFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueFixture extends ActiveFixture
{
    public $modelClass = Issue::class;
}
