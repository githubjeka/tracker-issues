<?php

namespace tracker\models;

use humhub\modules\content\components\ContentActiveRecord;
use Yii;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the model class for table "{{%tracker_issue}}". *
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $deadline
 * @property integer $status
 * @property integer $priority
 * @property Assignee[] $assignees
 * @property IssueContent $content
 */
class Issue extends ContentActiveRecord
{
    public $wallEntryClass = 'tracker\widgets\IssueWallEntry';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_issue}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['deadline',], 'safe'],
            [['status', 'priority',], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     * @return IssueQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new IssueQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function getContentName()
    {
        return Yii::t('TrackerIssuesModule.base', 'Issue');
    }

    /**
     * @inheritdoc
     */
    public function getContentDescription()
    {
        return $this->title;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignees()
    {
        return $this->hasMany(Assignee::className(), ['issue_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function getContent()
    {
        $tableName = IssueContent::tableName();
        return $this->hasOne(IssueContent::className(), ['object_id' => 'id'])
            ->andWhere(["$tableName.object_model" => self::className()]);
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('TrackerIssuesModule.views', 'Title'),
            'description' => \Yii::t('TrackerIssuesModule.views', 'Description'),
            'deadline' => \Yii::t('TrackerIssuesModule.views', 'Deadline'),
            'status' => \Yii::t('TrackerIssuesModule.views', 'Status'),
            'visibility' => \Yii::t('TrackerIssuesModule.views', 'Visibility'),
            'priority' => \Yii::t('TrackerIssuesModule.views', 'Priority'),
        ];
    }
}
