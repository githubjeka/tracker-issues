<?php

namespace tracker\models;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the model class for table "tracker_issues_tags".
 *
 * @property integer $id
 * @property integer $issue_id
 * @property integer $tag_id
 */
class TagsIssues extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_issues_tags}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['issue_id', 'tag_id'], 'required'],
            [['issue_id', 'tag_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     * @return TagsIssuesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagsIssuesQuery(get_called_class());
    }
}
