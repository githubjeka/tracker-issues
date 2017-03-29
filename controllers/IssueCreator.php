<?php

namespace tracker\controllers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;
use tracker\enum\IssueStatusEnum;
use tracker\models\Assignee;
use tracker\models\Issue;
use tracker\notifications\Assigned;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueCreator extends IssueService
{
    public function init()
    {
        $this->requestForm = new IssueRequest();
    }

    public function createDraft(ContentContainerActiveRecord $content)
    {
        $issueModel = new Issue();
        $issueModel->content->container = $content;

        if (!$issueModel->save()) {
            throw new \LogicException(json_encode($issueModel->errors));
        };

        $this->requestForm->id = $issueModel->id;
    }

    public function create()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $this->issueModel = Issue::findOne($this->requestForm->id);

        if ($this->issueModel === null) {
            throw new \LogicException();
        }

        $this->issueModel->title = $this->requestForm->title;
        $this->issueModel->description = $this->requestForm->description;
        $this->issueModel->status = $this->requestForm->status;
        $this->issueModel->priority = $this->requestForm->priority;
        $this->issueModel->deadline = ($this->requestForm->deadlineDate && $this->requestForm->deadlineTime)
            ? \Yii::$app->formatter->asDate($this->requestForm->deadlineDate, 'php:Y-m-d') . ' ' .
              $this->requestForm->deadlineTime
            : null;

        $this->issueModel->content->visibility = $this->requestForm->visibility;

        $this->issueModel->save(false);

        $this->issueModel->fileManager->attach(\Yii::$app->request->post('fileList'));

        if (is_array($this->requestForm->assignedUsers)) {

            if ($this->issueModel->status != IssueStatusEnum::TYPE_DRAFT && $this->requestForm->notifyAssignors) {
                $notification = new Assigned;
                $notification->source = $this->issueModel;
                $notification->originator = \Yii::$app->user->getIdentity();
            }

            foreach ($this->requestForm->assignedUsers as $userGuid) {
                $user = User::findOne(['guid' => $userGuid]);
                $assigneeModel = new Assignee();
                $assigneeModel->issue_id = $this->issueModel->id;
                $assigneeModel->user_id = $user->id;
                $assigneeModel->save(false);
                if (isset($notification)) {
                    $notification->send($user);
                }
            }
        }

        return $this->issueModel;
    }
}
