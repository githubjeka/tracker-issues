<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

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
 * @property null|integer $type id from tracker\models\DocumentType
 * @property null|integer $category id from tracker\models\DocumentCategory
 * @property integer $registered_at
 * @property integer $created_at
 * @property string $created_by
 * @property bool|int $access_for_all
 * @property DocumentReceiver[] $receivers
 * @property Issue[] $issues
 * @property DocumentType|null $typeModel
 * @property DocumentCategory|null $categoryModel
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
     * Returns List of DocumentType models
     *
     * @return DocumentType[]
     */
    public static function types()
    {
        return DocumentType::findByType(DocumentType::class)->all();
    }

    /**
     * Returns List of DocumentCategory models
     *
     * @return DocumentCategory[]
     */
    public static function categories()
    {
        return DocumentCategory::findByType(DocumentCategory::class)->all();
    }

    /**
     * @inheritdoc
     * @return DocumentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DocumentQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['access_for_all', 'default', 'value' => false],
            [['name', 'created_by', 'registered_at', 'created_at', 'access_for_all'], 'required'],
            [['registered_at', 'created_at', 'created_by'], 'integer'],
            [['description'], 'string'],
            [['type', 'category'], 'integer'],
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
            'access_for_all' => Yii::t('TrackerIssuesModule.views', 'Accessible to all'),
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
     * @return ActiveQuery
     */
    public function getTypeModel()
    {
        return $this->hasOne(DocumentType::class, ['id' => 'type']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCategoryModel()
    {
        return $this->hasOne(DocumentCategory::class, ['id' => 'category']);
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
}
