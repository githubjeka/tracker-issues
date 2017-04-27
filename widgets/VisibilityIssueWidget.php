<?php

namespace tracker\widgets;

use tracker\enum\IssueVisibilityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class VisibilityIssueWidget extends \yii\bootstrap\Widget
{
    public $visibilityContent;

    public function run()
    {
        $text = IssueVisibilityEnum::getLabel($this->visibilityContent);
        $class = $this->getCssClass();

        return "<span class=\"label $class\">$text</span>";
    }

    private function getCssClass()
    {
        switch ($this->visibilityContent) {
            case IssueVisibilityEnum::TYPE_PRIVATE :
                return 'label-danger';
            case IssueVisibilityEnum::TYPE_PROTECTED :
                return 'label-primary';
            case IssueVisibilityEnum::TYPE_PUBLIC :
                return 'label-success';
        }

        return 'label-default';
    }
}
