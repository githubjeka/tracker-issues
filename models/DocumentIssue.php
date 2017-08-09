<?php

namespace tracker\models;

/**
 * This is the model class for table "tracker_documents_issues".
 *
 * @property integer $id
 * @property integer $document_id
 * @property integer $issue_id
 * @property Document $document
 * @property Issue $issue
 */
class DocumentIssue extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tracker_documents_issues';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['document_id', 'issue_id'], 'required'],
            [['document_id', 'issue_id'], 'integer'],
            [
                ['document_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Document::class,
                'targetAttribute' => ['document_id' => 'id'],
            ],
            [
                ['issue_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Issue::class,
                'targetAttribute' => ['issue_id' => 'id'],
            ],
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::class, ['id' => 'document_id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIssue()
    {
        return $this->hasOne(Issue::class, ['id' => 'issue_id']);
    }
}
