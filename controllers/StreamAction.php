<?php

namespace tracker\controllers;

use humhub\modules\content\models\Content;
use humhub\modules\stream\actions\ContentContainerStream;
use tracker\enum\IssueStatusEnum;
use tracker\enum\IssueVisibilityEnum;
use tracker\models\Assignee;
use tracker\models\Issue;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class StreamAction extends ContentContainerStream
{
    public function setupCriteria()
    {
        if (empty($this->streamQuery->contentId)) {
            $tableIssue = Issue::tableName();
            $tableAssignee = Assignee::tableName();
            $tableContent = Content::tableName();
            $this->streamQuery
                ->query()
                ->leftJoin(
                    Issue::tableName(),
                    "object_id = $tableIssue.id AND content.object_model = :className",
                    [':className' => Issue::className()]
                )
                ->leftJoin(
                    Assignee::tableName(),
                    "$tableAssignee.issue_id = $tableIssue.id AND $tableAssignee.user_id = :user",
                    [':user' => \Yii::$app->user->id]
                )
                ->andWhere(
                    "$tableIssue.id IS NULL OR ($tableIssue.status != " . IssueStatusEnum::TYPE_DRAFT .
                    " AND ($tableContent.visibility != " . IssueVisibilityEnum::TYPE_PRIVATE .
                    " OR $tableAssignee.id IS NOT NULL OR $tableContent.created_by = :user))",
                    [':user' => \Yii::$app->user->id]
                );
        }
    }
}
