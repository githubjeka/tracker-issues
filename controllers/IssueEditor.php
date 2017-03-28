<?php

namespace tracker\controllers;

use humhub\modules\user\models\User;
use tracker\models\Assignee;
use tracker\models\Issue;
use yii\helpers\ArrayHelper;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueEditor extends IssueService
{
    public function __construct(Issue $issue, array $config = [])
    {
        $this->issueModel = $issue;
        $this->requestForm = new IssueRequest([
            'id' => $issue->id,
            'title' => $issue->title,
            'description' => $issue->description,
            'status' => $issue->status,
            'visibility' => $issue->content->visibility,
        ]);

        $assignedUsers = [];
        foreach ($this->issueModel->assignees as $assignee) {
            $assignedUsers[] = $assignee->user->guid;
        }
        $this->requestForm->assignedUsers = $assignedUsers;

        if ($issue->deadline) {
            $formatter = \Yii::$app->formatter;
            $this->requestForm->deadlineTime = $formatter->asTime($issue->deadline, 'php:H:m');
            $this->requestForm->deadlineDate = $formatter->asDate($issue->deadline, $formatter->dateInputFormat);
        }

        parent::__construct($config);
    }

    public function save()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $this->issueModel->title = $this->requestForm->title;
        $this->issueModel->description = $this->requestForm->description;
        $this->issueModel->status = $this->requestForm->status;
        $this->issueModel->deadline = ($this->requestForm->deadlineDate && $this->requestForm->deadlineTime)
            ? \Yii::$app->formatter->asDate($this->requestForm->deadlineDate, 'php:Y-m-d') . ' ' .
              $this->requestForm->deadlineTime
            : null;

        $this->issueModel->content->visibility = $this->requestForm->visibility;

        $this->issueModel->save(false);

        $oldAssignees = ArrayHelper::map($this->issueModel->getAssignees()->joinWith('user')
            ->all(), 'user_id', 'user.guid');

        foreach ($this->requestForm->assignedUsers as $userGuid) {

            $key = array_search($userGuid, $oldAssignees, true);

            if ($key === false) {
                $user = User::findOne(['guid' => $userGuid]);
                $assigneeModel = new Assignee();
                $assigneeModel->issue_id = $this->issueModel->id;
                $assigneeModel->user_id = $user->id;
                $assigneeModel->save(false);
            } else {
                $oldAssignees[$key] = null;
            }
        }

        foreach ($oldAssignees as $userId => $usrGuid) {
            if ($usrGuid !== null) {
                $assigneeModel = Assignee::findOne(['user_id' => $userId]);
                $assigneeModel->delete();
            }
        }

        $this->issueModel->fileManager->attach(\Yii::$app->request->post('fileList'));

        return $this->issueModel;
    }
}
