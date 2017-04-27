<?php

namespace tracker\widgets;

use tracker\enum\IssuePriorityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class PriorityIssueWidget extends \yii\bootstrap\Widget
{
    public $priority;

    public function run()
    {
        $text = IssuePriorityEnum::getLabel($this->priority);
        $class = $this->getCssClass();

        return "<span class=\"issue-label $class\">$text</span>";
    }

    private function getCssClass()
    {
        switch ($this->priority) {
            case IssuePriorityEnum::TYPE_URGENT :
                return 'issue-priority-urgent';
            case IssuePriorityEnum::TYPE_CRITICAL :
                return 'issue-priority-critical';
            case IssuePriorityEnum::TYPE_SERIOUS :
                return 'issue-priority-serious';
            case IssuePriorityEnum::TYPE_NORMAL :
                return 'issue-priority-normal';
            case IssuePriorityEnum::TYPE_MINOR :
                return 'issue-priority-minor';
        }

        return 'label-default';
    }
}
