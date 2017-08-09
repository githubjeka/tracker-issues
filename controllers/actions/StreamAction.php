<?php

namespace tracker\controllers\actions;

use humhub\modules\content\models\Content;
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
class StreamAction extends \humhub\modules\stream\actions\Stream
{
    /**
     * @var \humhub\modules\content\components\ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Limit to this content container
        $this->streamQuery
            ->query()
            ->andWhere(['content.contentcontainer_id' => $this->contentContainer->contentContainerRecord->id]);

        // Add all pinned contents to initial request
        if ($this->isInitialRequest()) {
            // Get number of pinned contents
            $pinnedQuery = clone $this->streamQuery->query();
            $pinnedQuery->andWhere(['content.pinned' => 1]);
            $pinnedCount = $pinnedQuery->count();

            // Increase query result limit to ensure there are also not pinned entries
            $this->streamQuery
                ->query()->limit += $pinnedCount;

            // Modify order - pinned content first
            $oldOrder = $this->streamQuery->query()->orderBy;
            $this->streamQuery->query()->orderBy("");
            $this->streamQuery->query()->addOrderBy('content.pinned DESC');
            $this->streamQuery->query()->addOrderBy($oldOrder);
        } else {
            // No pinned content in further queries
            $this->streamQuery->query()->andWhere("content.pinned = 0");
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
}
