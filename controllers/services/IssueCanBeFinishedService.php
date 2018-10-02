<?php

namespace tracker\controllers\services;

use tracker\models\Issue;

/**
 * Execute can_be_finished property for an issue
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueCanBeFinishedService
{
    private $issue;

    public function __construct(Issue $issue)
    {
        $this->issue = $issue;
    }

    /**
     * Issue can be finished?
     * @return bool
     */
    public function result()
    {
        $assignees = $this->issue->assignees;

        $issueCanBeFinished = true;
        foreach ($assignees as $assignee) {
            if (!$assignee['finish_mark']) {
                $issueCanBeFinished = false;
            }
        }

        if ($issueCanBeFinished && count($assignees) > 0) {
            return true;
        }

        return false;
    }

    /**
     * Updates Issue by result
     * @param null|bool $result
     * @param bool $needRefresh to refresh issue before check result
     * @return int
     */
    public function updateIssue($result = null, $needRefresh = true)
    {
        if ($needRefresh) {
            $this->issue->refresh();
        }

        if ($result === null) {
            $result = $this->result();
        }

        return $this->issue->updateAttributes(['can_be_finished' => $result]);
    }
}