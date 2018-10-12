<?php

namespace tracker\controllers\actions;

use humhub\modules\content\models\Content;
use humhub\modules\stream\actions\ContentContainerStream;
use tracker\enum\ContentVisibilityEnum;
use tracker\enum\IssueStatusEnum;
use tracker\models\Assignee;
use tracker\models\DocumentIssue;
use tracker\models\Issue;
use tracker\permissions\ViewAllDocuments;

/**
 * This code like \humhub\modules\stream\actions\ContentContainerStream
 * but permission for private issues should be others
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class StreamAction extends ContentContainerStream
{
    public $streamQueryClass = IssueWallStreamQuery::class;

    public function setupFilters()
    {
        if (!empty($this->streamQuery->contentId) && \Yii::$app->user->permissionmanager->can(new ViewAllDocuments())) {
            $tableIssue = Issue::tableName();
            $documentsIssuesTable = DocumentIssue::tableName();
            $this->streamQuery
                ->query()
                ->leftJoin($documentsIssuesTable, "$documentsIssuesTable.issue_id = $tableIssue.id")
                ->orWhere(
                    "$documentsIssuesTable.issue_id IS NOT NULL AND content.id = :id",
                    [':id' => $this->streamQuery->contentId]
                );
        }
    }

    public function setupCriteria()
    {
        if (empty($this->streamQuery->contentId)) {
            $this->streamQuery
                ->query()
                ->andWhere(
                    'content.object_model <> :className',
                    [':className' => Issue::class]
                );
        } else {
            /**
             * This code need to fix access by permanent link.
             */
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
                    "$tableIssue.id IS NULL OR ($tableContent.created_by = :user)" .
                    " OR ($tableIssue.status != " . IssueStatusEnum::TYPE_DRAFT .
                    " AND ($tableContent.visibility != " . ContentVisibilityEnum::TYPE_PRIVATE .
                    " OR $tableAssignee.id IS NOT NULL)) ",
                    [':user' => \Yii::$app->user->id]
                );
        }
    }
}
