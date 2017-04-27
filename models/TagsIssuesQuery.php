<?php

namespace tracker\models;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the ActiveQuery class for [[TagsIssues]].
 *
 * @see TagsIssues
 */
class TagsIssuesQuery extends \yii\db\ActiveQuery
{
    public function byUser($userId)
    {
        return $this->andWhere(['owner_id' => $userId]);
    }

    /**
     * @inheritdoc
     * @return TagsIssues[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return TagsIssues|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
