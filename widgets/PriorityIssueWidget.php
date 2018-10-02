<?php

namespace tracker\widgets;

use tracker\enum\IssuePriorityEnum;
use yii\helpers\Html;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class PriorityIssueWidget extends \yii\bootstrap\Widget
{
    /**
     * @var int one of IssuePriorityEnum
     */
    public $priority;

    /**
     * For example `btn-block`
     *
     * @var string
     */
    public $extraCssClass;

    public function run()
    {
        $text = IssuePriorityEnum::getLabel($this->priority);
        $class = $this->getCssClass();
        if (is_string($this->extraCssClass)) {
            $class .= ' ' . $this->extraCssClass;
        }
        return Html::tag('span', $text, ['class' => "issue-label $class"]);
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
