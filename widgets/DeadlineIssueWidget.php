<?php

namespace tracker\widgets;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DeadlineIssueWidget extends \yii\bootstrap\Widget
{
    /**
     * @var int|string|\DateTime
     * @see \yii\i18n\Formatter::asDatetime()
     */
    public $deadline;
    /**
     * @var int|string|\DateTime
     * @see \yii\i18n\Formatter::asDatetime()
     */
    public $startTime;
    /**
     * The indicator for adding "Must be completed by" message
     *
     * @var bool
     */
    public $short = false;

    public function run()
    {
        return $this->render('deadlineIssue',
            [
                'startTime' => $this->startTime,
                'short' => $this->short,
                'deadline' => $this->deadline,
            ]
        );
    }
}
