<?php

namespace tracker\models;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the ActiveQuery class for [[Tag]].
 *
 * @see Tag
 */
class TagQuery extends \yii\db\ActiveQuery
{
    /**
     * @param $userId string|integer
     *
     * @return $this
     */
    public function byUser($userId)
    {
        return $this->andWhere([Tag::tableName() . '.owner_id' => $userId]);
    }

    /**
     * @inheritdoc
     * @return Tag[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Tag|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
