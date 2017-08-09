<?php

namespace tracker\models;

use Yii;

/**
 * This is the model class for table "{{%tracker_document_files}}".
 *
 * @property integer $id
 * @property integer $document_id
 * @property string $filename
 * @property integer $is_show
 * @property string $comments
 * @property integer $created_at
 * @property integer $created_by
 * @property Document $document
 */
class DocumentFile extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_document_files}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'filename', 'created_at', 'created_by'], 'required'],
            [['document_id', 'created_at', 'created_by'], 'integer'],
            [['is_show'], 'boolean'],
            [['comments'], 'string'],
            [['filename'], 'string', 'max' => 255],
            [
                ['document_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Document::className(),
                'targetAttribute' => ['document_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('TrackerIssuesModule.views', 'ID'),
            'document_id' => Yii::t('TrackerIssuesModule.views', 'Document ID'),
            'filename' => Yii::t('TrackerIssuesModule.views', 'Filename'),
            'is_show' => Yii::t('TrackerIssuesModule.views', 'Is Show'),
            'comments' => Yii::t('TrackerIssuesModule.views', 'Comments'),
            'created_at' => Yii::t('TrackerIssuesModule.views', 'Created At'),
            'created_by' => Yii::t('TrackerIssuesModule.views', 'Created By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::class, ['id' => 'document_id']);
    }
}
