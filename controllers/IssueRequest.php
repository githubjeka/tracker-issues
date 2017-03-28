<?php

namespace tracker\controllers;

use tracker\enum\IssueStatusEnum;
use tracker\enum\IssueVisibilityEnum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueRequest extends \yii\base\Model
{
    public $id;
    public $title;
    public $description;
    public $deadlineDate;
    public $deadlineTime;
    public $assignedUsers = [];
    public $status = IssueStatusEnum::TYPE_DRAFT;
    public $visibility = IssueVisibilityEnum::TYPE_PROTECTED;

    public function rules()
    {
        return [
            ['assignedUsers', 'default', 'value' => []],
            ['status', 'default', 'value' => IssueStatusEnum::TYPE_DRAFT],
            ['visibility', 'default', 'value' => IssueVisibilityEnum::TYPE_PROTECTED],
            [['id', 'title'], 'required'],
            ['description', 'safe'],
            ['title', 'string', 'max' => 255],
            ['deadlineDate', 'date', 'format' => \Yii::$app->formatter->dateInputFormat],
            ['deadlineTime', 'time', 'format' => 'php:H:m'],
            ['status', 'in', 'range' => array_keys(IssueStatusEnum::getList())],
            ['visibility', 'in', 'range' => array_keys(IssueVisibilityEnum::getList())],
            ['assignedUsers', 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'title' => \Yii::t('TrackerIssuesModule.views', 'Title'),
            'description' => \Yii::t('TrackerIssuesModule.views', 'Description'),
            'deadline' => \Yii::t('TrackerIssuesModule.views', 'Deadline'),
            'deadlineDate' => \Yii::t('TrackerIssuesModule.views', 'Deadline Date'),
            'deadlineTime' => \Yii::t('TrackerIssuesModule.views', 'Deadline Time'),
            'status' => \Yii::t('TrackerIssuesModule.views', 'Status'),
            'assignedUsers' => \Yii::t('TrackerIssuesModule.views', 'Assigned Users'),
            'visibility' => \Yii::t('TrackerIssuesModule.views', 'Visibility'),
        ];
    }
}
