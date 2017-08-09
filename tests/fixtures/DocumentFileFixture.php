<?php

namespace tracker\tests\fixtures;

use tracker\models\DocumentFile;
use yii\test\ActiveFixture;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentFileFixture extends ActiveFixture
{
    public $modelClass = DocumentFile::class;
    public $depends = [DocumentFixture::class,];
}
