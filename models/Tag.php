<?php

namespace tracker\models;

use Yii;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * This is the model class for table "tracker_tag".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $bg_color
 * @property string $text_color
 * @property integer $owner_id
 * @property Issue[] $issues
 */
class Tag extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tracker_tag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['bg_color', 'text_color'], 'string'],
            [['name'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => Yii::t('TrackerIssuesModule.views', 'Name'),
            'description' => Yii::t('TrackerIssuesModule.views', 'Description'),
            'bg_color' => Yii::t('TrackerIssuesModule.views', 'Background Color'),
            'text_color' => Yii::t('TrackerIssuesModule.views', 'Text Color'),
        ];
    }

    /**
     * @inheritdoc
     * @return TagQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TagQuery(get_called_class());
    }

    public function getIssues()
    {
        return $this->hasMany(Issue::class, ['id' => 'issue_id'])
            ->viaTable(TagsIssues::tableName(), ['tag_id' => 'id']);
    }
}
