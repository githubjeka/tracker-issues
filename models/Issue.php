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
 * @property string $started_at
 * @property string $finished_at
 * @property Assignee[] $assignees
 * @property Tag[] $tags
 * @property Tag[] $personalTags
 * @property IssueContent $content
 * @property Issue|null $parent
 * @property Issue[] $subtasks
 * @property Document[] $documents
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
            [['deadline', 'started_at'], 'date', 'format' => 'php:Y-m-d H:i'],
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
     * @return TagQuery|\yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable(TagsIssues::tableName(), ['issue_id' => 'id']);
    }

    /**
     * @return TagQuery|\yii\db\ActiveQuery
     */
    public function getPersonalTags()
    {
        return $this->getTags()->byUser(\Yii::$app->user->id);
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
     * @return IssueQuery|\yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Issue::class, ['id' => 'parent_id'])
            ->viaTable(Link::tableName(), ['child_id' => 'id']);
    }

    /**
     * @return IssueQuery|\yii\db\ActiveQuery
     */
    public function getSubtasks()
    {
        return $this->hasMany(Issue::class, ['id' => 'child_id'])
            ->viaTable(Link::tableName(), ['parent_id' => 'id']);
    }

    public function getDocuments()
    {
        return $this->hasMany(Document::class, ['id' => 'document_id'])
            ->viaTable(DocumentIssue::tableName(), ['issue_id' => 'id']);
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
            'finished_at' => \Yii::t('TrackerIssuesModule.views', 'Finished at'),
            'started_at' => \Yii::t('TrackerIssuesModule.views', 'Started at'),
        ];
    }
}
