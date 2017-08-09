<?php

namespace tracker\models;

use humhub\modules\content\components\ActiveQueryContent;
use humhub\modules\content\models\Content;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\enum\IssueStatusEnum;
use tracker\enum\ContentVisibilityEnum;

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
     * @return $this
     */
    public function readable($user = null)
    {
        if ($user === null && !\Yii::$app->user->isGuest) {
            $user = \Yii::$app->user->getIdentity();
        }

        $this->joinWith(['content', 'content.contentContainer', 'content.createdBy']);

        if ($user !== null) {
            $this->leftJoin('space_membership',
                'contentcontainer.pk=space_membership.space_id AND contentcontainer.class=:spaceClass AND space_membership.user_id=:userId',
                [':userId' => $user->id, ':spaceClass' => Space::className()]);
            $this->leftJoin('space', 'contentcontainer.pk=space.id AND contentcontainer.class=:spaceClass',
                [':spaceClass' => Space::className()]);
            $this->leftJoin('user cuser', 'contentcontainer.pk=cuser.id AND contentcontainer.class=:userClass',
                [':userClass' => User::className()]);

            // Build Access Check based on Space Content Container
            $conditionSpace = 'space.id IS NOT NULL AND (';                                         // space content
            $conditionSpace .= ' (space_membership.status=3)';                                      // user is space member
            $conditionSpace .= ' OR (content.visibility=1 AND space.visibility != 0)';               // visibile space and public content
            $conditionSpace .= ')';

            // Build Access Check based on User Content Container
            $conditionUser = 'cuser.id IS NOT NULL AND (';                                         // user content
            $conditionUser .= '   (content.visibility = 1) OR';                                     // public visible content
            $conditionUser .= '   ((content.visibility = 0 OR content.visibility = 2) AND content.created_by=' . $user->id .
                              ')';  // private content of user
            if (\Yii::$app->getModule('friendship')->getIsEnabled()) {
                $this->leftJoin('user_friendship cff', 'cuser.id=cff.user_id AND cff.friend_user_id=:fuid',
                    [':fuid' => $user->id]);
                $conditionUser .= ' OR (content.visibility = 0 AND cff.id IS NOT NULL)';  // users are friends
            }
            $conditionUser .= ')';

            $this->andWhere("{$conditionSpace} OR {$conditionUser}");

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
                    "$tableContent.visibility != " . ContentVisibilityEnum::TYPE_PRIVATE .
                    " OR $tableContent.created_by = :user OR $tableAssignee.id IS NOT NULL",
                    [':user' => $user->id]
                );
        } else {
            $this->leftJoin('space', 'contentcontainer.pk=space.id AND contentcontainer.class=:spaceClass',
                [':spaceClass' => Space::className()]);
            $this->andWhere('space.id IS NOT NULL and space.visibility=' . Space::VISIBILITY_ALL .
                            ' AND content.visibility=1');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function withoutDeadline()
    {
        $this->andWhere('deadline IS NULL');
        return $this;
    }

    /**
     * @return $this
     */
    public function withDeadline()
    {
        $this->andWhere('deadline IS NOT NULL');
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
