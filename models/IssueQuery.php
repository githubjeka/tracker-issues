<?php

namespace tracker\models;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\models\Content;
use tracker\enum\IssueStatusEnum;
use tracker\enum\IssueVisibilityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueQuery extends ActiveQueryContent
{
    /**
     * Only returns user readable records
     *
     * @param \humhub\modules\user\models\User $user
     *
     * @return \humhub\modules\content\components\ActiveQueryContent
     */
    public function readable($user = null)
    {
        if ($user === null && !\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->getIdentity();
        }

        parent::readable($user);

        $tableIssue = Issue::tableName();
        $tableAssignee = Assignee::tableName();
        $tableContent = Content::tableName();

        $this
            ->leftJoin(
                Assignee::tableName(),
                "$tableAssignee.issue_id = $tableIssue.id AND $tableAssignee.user_id = :user",
                [':user' => $user->id]
            )
            ->andWhere(
                "$tableIssue.status != " . IssueStatusEnum::TYPE_DRAFT . " OR $tableContent.created_by = :user",
                [':user' => $user->id]
            )
            ->andWhere(
                "$tableContent.visibility != " . IssueVisibilityEnum::TYPE_PRIVATE .
                " OR $tableContent.created_by = :user OR $tableAssignee.id IS NOT NULL",
                [':user' => $user->id]
            );

        return $this;
    }

    /**
     * @inheritdoc
     * @return Issue[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Issue|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
