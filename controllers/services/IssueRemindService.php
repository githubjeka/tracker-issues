<?php

namespace tracker\controllers\services;

use humhub\modules\user\models\User;
use tracker\models\Issue;
use tracker\notifications\RemindIssue;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueRemindService
{
    /**
     * @var Issue
     */
    private $issue;
    /**
     * @var User
     */
    private $originator;
    /**
     * @var User
     */
    private $user;

    public function __construct(Issue $issue, User $originator, User $user)
    {
        $this->issue = $issue;
        $this->user = $user;
        $this->originator = $originator;
    }

    public function sendNotify()
    {
        $notification = new RemindIssue();
        $notification->source = $this->issue;
        $notification->originator = $this->originator;
        $notification->send($this->user);
    }
}
