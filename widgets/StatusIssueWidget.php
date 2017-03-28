<?php

namespace tracker\widgets;

use tracker\enum\IssueStatusEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class StatusIssueWidget extends \yii\bootstrap\Widget
{
    public $status;

    public function run()
    {
        if ($this->status == IssueStatusEnum::TYPE_FINISHED) {
            $style = 'primary';
        } elseif ($this->status == IssueStatusEnum::TYPE_WORK) {
            $style = 'warning';
        } else {
            $style = 'default';
        }

        return '<span class="label label-' . $style . '">' . IssueStatusEnum::getLabel($this->status) . '</span>';
    }
}
