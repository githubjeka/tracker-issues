<?php

namespace tracker\models;

use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%document}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $number
 * @property string $from
 * @property string $to
 * @property integer $type
 * @property integer $category
 * @property integer $registered_at
 * @property integer $created_at
 * @property string $created_by
 * @property DocumentReceiver[] $receivers
 * @property Issue[] $issues
 * @property DocumentFile $file
 */
class Document extends yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_document}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'created_by', 'registered_at', 'created_at'], 'required'],
            [['registered_at', 'created_at', 'created_by'], 'integer'],
            [['description'], 'string'],
            [['type', 'category'], 'string'],
            [['number'], 'string', 'max' => 15],
            [['name', 'from', 'to'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('TrackerIssuesModule.views', 'Name'),
            'file' => Yii::t('TrackerIssuesModule.views', 'File'),
            'description' => Yii::t('TrackerIssuesModule.views', 'Resolution, description'),
            'number' => Yii::t('TrackerIssuesModule.views', 'Number'),
            'from' => Yii::t('TrackerIssuesModule.views', 'From'),
            'to' => Yii::t('TrackerIssuesModule.views', 'To'),
            'type' => Yii::t('TrackerIssuesModule.views', 'Type'),
            'category' => Yii::t('TrackerIssuesModule.views', 'Category'),
            'registered_at' => Yii::t('TrackerIssuesModule.views', 'Registered at'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReceivers()
    {
        return $this->hasMany(DocumentReceiver::className(), ['document_id' => 'id'])->inverseOf('document');
    }

    /**
     * TODO убрать это, сделать по аналогии с категориями
     *
     * @return array
     */
    public static function types()
    {
        return [
            'fax' => Yii::t('TrackerIssuesModule.views', 'Fax'),
            'email' => Yii::t('TrackerIssuesModule.views', 'EMail'),
            'lotus' => Yii::t('TrackerIssuesModule.views', 'EMail Lotus'),
            'mail' => Yii::t('TrackerIssuesModule.views', 'Letter'),
            'ordered' => Yii::t('TrackerIssuesModule.views', 'Ordered Letter'),
            'ordered-with-notify' => Yii::t('TrackerIssuesModule.views', 'Ordered Letter with notification'),
            'ordered-with-ordered-notify' => Yii::t('TrackerIssuesModule.views',
                'Ordered Letter with ordered notification'),
        ];
    }

    /**
     * TODO: should be created via user interface and stored in database
     *
     * @return array where key is index of category, should be valid to create path on store of documents,
     * value - string, name of category.
     */
    public static function categories()
    {
        return isset(Yii::$app->params['categories-document']) && !empty(Yii::$app->params['categories-document'])
            ? Yii::$app->params['categories-document']
            : [];
    }

    /** @return IssueQuery|ActiveQuery */
    public function getIssues()
    {
        return $this->hasMany(Issue::class, ['id' => 'issue_id'])
            ->viaTable(DocumentIssue::tableName(), ['document_id' => 'id']);
    }

    public function getFile()
    {
        return $this->hasOne(DocumentFile::class, ['document_id' => 'id'])->andWhere(['is_show' => true]);
    }

    /**
     * @inheritdoc
     * @return DocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }
}
