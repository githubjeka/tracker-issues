<?php

namespace tracker\widgets;

use tracker\enum\ContentVisibilityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class VisibilityIssueWidget extends \yii\bootstrap\Widget
{
    public $visibilityContent;

    public function run()
    {
        $text = ContentVisibilityEnum::getLabel($this->visibilityContent);
        $class = $this->getCssClass();

        return "<span class=\"label $class\">$text</span>";
    }

    private function getCssClass()
    {
        switch ($this->visibilityContent) {
            case ContentVisibilityEnum::TYPE_PRIVATE :
                return 'label-danger';
            case ContentVisibilityEnum::TYPE_PROTECTED :
                return 'label-primary';
            case ContentVisibilityEnum::TYPE_PUBLIC :
                return 'label-success';
        }

        return 'label-default';
    }
}
