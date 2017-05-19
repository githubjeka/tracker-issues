<?php

namespace tracker\widgets;

use tracker\enum\IssueStatusEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class StatusIssueWidget extends \yii\bootstrap\Widget
{
    public $status;
    public $asCheckboxIcon = false;

    public function run()
    {
        if ($this->status == IssueStatusEnum::TYPE_FINISHED) {
            $style = 'primary';
        } elseif ($this->status == IssueStatusEnum::TYPE_WORK) {
            $style = 'warning';
        } else {
            $style = 'default';
        }

        if ($this->asCheckboxIcon) {
            if ($this->status === IssueStatusEnum::TYPE_FINISHED) {
                $text = '<span class="fa fa-check" aria-hidden="true"></span>';
            } else {
                $text = '<span class="fa fa-square-o" aria-hidden="true"></span>';
            }
        } else {
            $text = IssueStatusEnum::getLabel($this->status);
        }

        return '<span class="label label-' . $style . '">' . $text . '</span>';
    }
}
