<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\models;

use humhub\modules\content\models\ContentTag;
use tracker\Module;

/**
 * Model for category of a document
 */
class DocumentCategory extends ContentTag
{
    public function init()
    {
        $this->moduleId = Module::getIdentifier();
        $this->color = '#333';
        parent::init();
    }
}
