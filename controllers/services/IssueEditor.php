<?php

namespace tracker\controllers\services;

use humhub\modules\user\models\User;
use tracker\enum\IssueStatusEnum;
use tracker\models\Assignee;
use tracker\models\Issue;
use tracker\models\Tag;
use tracker\models\TagsIssues;
use tracker\notifications\Assigned;
use tracker\notifications\IssueEdited;
use yii\helpers\ArrayHelper;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueEditor extends IssueService
{
    public function __construct(Issue $issue, array $config = [])
    {
        parent::__construct($config);

        $this->issueModel = $issue;

        $this->requestForm->id = $issue->id;
        $this->requestForm->title = $issue->title;
        $this->requestForm->description = $issue->description;
        $this->requestForm->status = $issue->status;
        $this->requestForm->visibility = $issue->content->visibility;
        $this->requestForm->priority = $issue->priority;

        $assignedUsers = [];
        foreach ($this->issueModel->assignees as $assignee) {
            $assignedUsers[] = $assignee->user->guid;
        }
        $this->requestForm->assignedUsers = $assignedUsers;

        $tags = [];
        foreach ($this->issueModel->personalTags as $tag) {
            $tags[] = $tag->id;
        }
        $this->requestForm->tags = $tags;

        $formatter = \Yii::$app->formatter;
        $this->requestForm->startedTime = $formatter->asTime($issue->started_at, 'php:H:i');
        $this->requestForm->startedDate = $formatter->asDate($issue->started_at, 'php:Y-m-d');

        if ($issue->deadline) {
            $this->requestForm->deadlineTime = $formatter->asTime($issue->deadline, 'php:H:i');
            $this->requestForm->deadlineDate = $formatter->asDate($issue->deadline, 'php:Y-m-d');
        }

        if (!$this->requestForm->validate()) {
            throw new \LogicException();
        }
    }

    /**
     * @return Issue|false
     */
    public function save()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $currentUser = \Yii::$app->user->getIdentity();

        $this->issueModel->title = $this->requestForm->title;
        $this->issueModel->description = $this->requestForm->description;
        $this->issueModel->status = $this->requestForm->status;
        $this->issueModel->priority = $this->requestForm->priority;

        if ($this->requestForm->startedDate && $this->requestForm->startedTime) {
            $this->issueModel->started_at = $this->requestForm->startedDate . ' ' . $this->requestForm->startedTime;
        }

        $this->issueModel->deadline = ($this->requestForm->deadlineDate && $this->requestForm->deadlineTime)
            ? $this->requestForm->deadlineDate . ' ' . $this->requestForm->deadlineTime . ':00'
            : null;

        $this->issueModel->content->visibility = $this->requestForm->visibility;

        if ($this->issueModel->status == IssueStatusEnum::TYPE_FINISHED) {
            $this->issueModel->finished_at = date('Y-m-d H:i');
        } else {
            $this->issueModel->status = IssueStatusEnum::TYPE_WORK;
        }

        if (!$this->issueModel->save(false)) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($this->issueModel->getErrors()));
        };

        $oldAssignees = ArrayHelper::map($this->issueModel->getAssignees()->joinWith('user')
            ->all(), 'user_id', 'user.guid');

        foreach ($this->requestForm->assignedUsers as $userGuid) {

            $key = array_search($userGuid, $oldAssignees, true);

            if ($key === false) {
                $user = User::findOne(['guid' => $userGuid]);
                $assigneeModel = new Assignee();
                $assigneeModel->issue_id = $this->issueModel->id;
                $assigneeModel->user_id = $user->id;
                $assigneeModel->created_at = date('Y-m-d H:i');
                if (!$assigneeModel->save(false)) {
                    $transaction->rollBack();
                    throw new \LogicException(json_encode($assigneeModel->getErrors()));
                };
                if ($this->issueModel->status != IssueStatusEnum::TYPE_DRAFT && $this->requestForm->notifyAssignors) {
                    $notification = new Assigned;
                    $notification->source = $this->issueModel;
                    $notification->originator = \Yii::$app->user->getIdentity();
                    $notification->send($user);
                }
            } else {
                $oldAssignees[$key] = null;
            }
        }

        foreach ($oldAssignees as $userId => $usrGuid) {

            if ($usrGuid !== null) {
                $assigneeModel = Assignee::findOne(['user_id' => $userId, 'issue_id' => $this->issueModel->id]);
                if (!$assigneeModel->delete()) {
                    $transaction->rollBack();
                    throw new \LogicException('Assignee model can not be deleted');
                };
            }

            if ($this->issueModel->status != IssueStatusEnum::TYPE_DRAFT && $this->requestForm->notifyAssignors) {
                $notification = new IssueEdited();
                $notification->source = $this->issueModel;
                $notification->originator = $currentUser;
                $notification->send(User::findOne($userId));
            }
        }

        $oldTags = $this->issueModel->personalTags;

        foreach ($this->requestForm->tags as $tagId) {

            $key = array_search($tagId, $oldTags, true);

            if ($key === false) {
                $tagModel = Tag::find()->byUser($currentUser->getId())->andWhere(['id' => $tagId])->one();
                $relationModel = new TagsIssues();
                $relationModel->issue_id = $this->issueModel->id;
                $relationModel->tag_id = $tagModel->id;
                if (!$relationModel->save(false)) {
                    $transaction->rollBack();
                    throw new \LogicException(json_encode($relationModel->getErrors()));
                };
            } else {
                $oldTags[$key] = null;
            }
        }

        foreach ($oldTags as $tagModel) {
            if ($tagModel !== null) {
                $relationModel = TagsIssues::find()->where([
                    'issue_id' => $this->issueModel->id,
                    'tag_id' => $tagModel->id,
                ])->one();
                if (!$relationModel->delete()) {
                    $transaction->rollBack();
                    throw new \LogicException('TagsIssues model can not be deleted');
                };
            }
        }

        $this->issueModel->fileManager->attach(\Yii::$app->request->post('fileList'));

        $transaction->commit();
        return $this->issueModel;
    }
}
