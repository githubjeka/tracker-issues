<?php

namespace tracker\controllers;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\user\models\User;
use tracker\enum\IssueStatusEnum;
use tracker\models\Assignee;
use tracker\models\Issue;
use tracker\models\Tag;
use tracker\models\TagsIssues;
use tracker\notifications\Assigned;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueCreator extends IssueService
{
    public function createDraft(ContentContainerActiveRecord $content)
    {
        $issueModel = new Issue();
        $issueModel->content->notifyUsersOfNewContent = false;
        $issueModel->content->setContainer($content);

        if (!$issueModel->save()) {
            throw new \LogicException(json_encode($issueModel->errors));
        };

        $issueModel->refresh();
        $this->requestForm->id = $issueModel->id;

        return $issueModel;
    }

    /**
     * @return false|Issue
     */
    public function create()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $this->issueModel = Issue::findOne($this->requestForm->id);

        if ($this->issueModel === null) {
            throw new \LogicException();
        }

        $currentUser = \Yii::$app->user->getIdentity();

        $this->issueModel->title = $this->requestForm->title;
        $this->issueModel->description = $this->requestForm->description;
        $this->issueModel->status = IssueStatusEnum::TYPE_WORK;
        $this->issueModel->priority = $this->requestForm->priority;
        $this->issueModel->deadline = ($this->requestForm->deadlineDate && $this->requestForm->deadlineTime)
            ? $this->requestForm->deadlineDate . ' ' . $this->requestForm->deadlineTime . ':00'
            : null;

        $this->issueModel->content->visibility = $this->requestForm->visibility;
        $this->issueModel->started_at = date('Y-m-d H:i');
        if ($this->issueModel->status == IssueStatusEnum::TYPE_FINISHED) {
            $this->issueModel->finished_at = date('Y-m-d H:i');
        }
        $this->issueModel->save(false);

        $this->issueModel->fileManager->attach(\Yii::$app->request->post('fileList'));

        if (is_array($this->requestForm->assignedUsers)) {

            if ($this->issueModel->status != IssueStatusEnum::TYPE_DRAFT && $this->requestForm->notifyAssignors) {
                $notification = new Assigned;
                $notification->source = $this->issueModel;
                $notification->originator = $currentUser;
            }

            foreach ($this->requestForm->assignedUsers as $userGuid) {
                // TODO User validate or filters
                $user = User::findOne(['guid' => $userGuid]);
                $assigneeModel = new Assignee();
                $assigneeModel->issue_id = $this->issueModel->id;
                $assigneeModel->user_id = $user->id;
                $assigneeModel->created_at = date('Y-m-d H:i');
                $assigneeModel->save(false);
                if (isset($notification)) {
                    $notification->send($user);
                }
            }
        }

        if (is_array($this->requestForm->tags)) {

            foreach ($this->requestForm->tags as $tagId) {
                // TODO Tag validate or filters
                $tagModel = Tag::find()->byUser($currentUser->getId())->andWhere(['id' => $tagId])->one();
                $relationModel = new TagsIssues();
                $relationModel->issue_id = $this->issueModel->id;
                $relationModel->tag_id = $tagModel->id;
                $relationModel->save(false);
            }
        }

        return $this->issueModel;
    }
}
