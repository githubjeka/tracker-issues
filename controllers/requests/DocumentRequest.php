<?php

namespace tracker\controllers\requests;

use yii\web\UploadedFile;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentRequest extends \yii\base\Model
{
    public $name;
    public $description;
    public $from;
    public $registeredAt;
    public $to;
    /** @var  UploadedFile */
    public $file;
    public $number;
    public $type;
    public $category;
    /** @var array of uid users to DocumentReceiver */
    public $receivers = [];

    public function rules()
    {
        return [
            [['receivers'], 'default', 'value' => []],
            ['receivers', 'each', 'rule' => ['string']],
            [['name', 'registeredAt', 'number'], 'required'],
            [['registeredAt',], 'date', 'format' => 'php:Y-m-d'],
            [['category', 'type'], 'safe'],
            [['name', 'from', 'to', 'number'], 'string', 'max' => 255],
            ['file', 'file', 'skipOnEmpty' => false,],
            ['description', 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('TrackerIssuesModule.views', 'Name'),
            'file' => \Yii::t('TrackerIssuesModule.views', 'File'),
            'description' => \Yii::t('TrackerIssuesModule.views', 'Resolution, description'),
            'number' => \Yii::t('TrackerIssuesModule.views', 'Number'),
            'from' => \Yii::t('TrackerIssuesModule.views', 'From'),
            'to' => \Yii::t('TrackerIssuesModule.views', 'To'),
            'type' => \Yii::t('TrackerIssuesModule.views', 'Type'),
            'category' => \Yii::t('TrackerIssuesModule.views', 'Category'),
            'receivers' => \Yii::t('TrackerIssuesModule.views', 'Receivers'),
            'registeredAt' => \Yii::t('TrackerIssuesModule.views', 'Registered at'),
        ];
    }
}
