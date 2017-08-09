<?php

namespace tracker\tests\fixtures;

use tracker\models\Document;
use yii\test\ActiveFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentFixture extends ActiveFixture
{
    public $modelClass = Document::class;
    public $depends = [
        DocumentReceiversFixture::class,
        DocumentIssuesFixture::class,
        IssueFixture::class,
    ];
}
