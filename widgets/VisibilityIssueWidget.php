<?php

namespace tracker\widgets;

use tracker\enum\IssueVisibilityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class VisibilityIssueWidget extends \yii\bootstrap\Widget
{
    public $isAllowedGuest;
    public $visibilitySpace;
    public $visibilityContent;

    public function run()
    {
        if ($this->visibilityContent == IssueVisibilityEnum::TYPE_PRIVATE) {
            return '<span class="label label-danger">' . \Yii::t('TrackerIssuesModule.enum', 'Private') . '</span>';
        }
        if ($this->visibilityContent == IssueVisibilityEnum::TYPE_PROTECTED) {
            return '<span class="label label-primary">' . \Yii::t('TrackerIssuesModule.enum', 'Protected') . '</span>';
        }
        if ($this->visibilityContent == IssueVisibilityEnum::TYPE_PUBLIC) {
            return '<span class="label label-success">' . \Yii::t('TrackerIssuesModule.enum', 'Public') . '</span>';
        }
    }
}
