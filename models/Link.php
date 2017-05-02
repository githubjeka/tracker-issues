<?php

namespace tracker\models;

/**
 * This is the model class for table "tracker_links".
 *
 * @property integer $id
 * @property integer $type
 * @property integer $parent_id
 * @property integer $child_id
 *
 * @property Issue $child
 * @property Issue $parent
 */
class Link extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_links}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'parent_id', 'child_id'], 'required'],
            [['type', 'parent_id', 'child_id'], 'integer'],
            [['child_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['child_id' => 'id']],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Issue::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChild()
    {
        return $this->hasOne(Issue::class, ['id' => 'child_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Issue::class, ['id' => 'parent_id']);
    }
}
