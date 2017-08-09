<?php

namespace tracker\models;

use humhub\modules\content\components\ContentActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "{{%document}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $file
 * @property string $description
 * @property string $number
 * @property string $from
 * @property string $to
 * @property integer $type
 * @property integer $category
 * @property DocumentReceiver[] $receivers
 * @property Issue[] $issues
 */
class Document extends ContentActiveRecord
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
            [['name', 'file'], 'required'],
            [['description'], 'string'],
            [['type', 'category'], 'string'],
            [['name', 'file', 'number', 'from', 'to'], 'string', 'max' => 255],
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


    /**
     * @inheritdoc
     * @return DocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }
}
