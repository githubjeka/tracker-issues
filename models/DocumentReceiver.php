<?php

namespace tracker\models;

use humhub\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "{{%tracker_receiver_document}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $document_id
 * @property integer $view_mark
 * @property string $created_at
 * @property string $viewed_at
 * @property Document $document
 * @property User $user
 */
class DocumentReceiver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_receiver_document}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'document_id'], 'required'],
            [['user_id', 'document_id', 'view_mark'], 'integer'],
            [['created_at', 'viewed_at'], 'date', 'format' => 'php:Y-m-d H:i'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getDocument()
    {
        return $this->hasOne(Document::className(), ['id' => 'document_id'])->inverseOf('receivers');
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
