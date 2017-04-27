<?php

namespace tracker\models;

use humhub\modules\user\models\User;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the model class for table "{{%tracker_assignee}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $issue_id
 * @property integer $view_mark
 * @property integer $finish_mark
 * @property string $created_at
 * @property string $viewed_at
 * @property string $finished_at
 * @property User $user
 * @property Issue $issue
 */
class Assignee extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_assignee}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'issue_id'], 'required'],
            [['user_id', 'issue_id', 'view_mark', 'finish_mark'], 'integer'],
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getIssue()
    {
        return $this->hasOne(Issue::className(), ['id' => 'issue_id']);
    }
}
