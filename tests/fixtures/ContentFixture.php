<?php

namespace tracker\tests\fixtures;

use humhub\modules\content\models\Content;
use yii\test\ActiveFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class ContentFixture extends ActiveFixture
{
    public $modelClass = Content::class;
}
