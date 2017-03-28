<?php

namespace tracker\widgets;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DeadlineIssueWidget extends \yii\bootstrap\Widget
{
    public $deadline;
    public $short = false;

    public function run()
    {
        if ($this->deadline) {
            if ($this->short) {
                echo '<span class="label label-danger">' .
                     \Yii::$app->formatter->asDatetime($this->deadline, 'short') .
                     '</span>';
            } else {
                echo '<span class="label label-danger">' .
                     \Yii::t('TrackerIssuesModule.views', 'Must be completed by') .
                     '&nbsp;' . \Yii::$app->formatter->asDatetime($this->deadline, 'short') .
                     '</span>';
            }
        } else {
            echo '<span class="label label-default">'
                 . \Yii::t('TrackerIssuesModule.views', 'Has not deadline') .
                 '</span>';
        }
    }
}
