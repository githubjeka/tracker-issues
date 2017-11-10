<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\models;

use humhub\modules\content\models\ContentTag;
use tracker\Module;

/**
 * Model for type of a document
 */
class DocumentType extends ContentTag
{
    public function init()
    {
        $this->moduleId = Module::getIdentifier();
        $this->color = '#afe7ed';
        parent::init();
    }
}
