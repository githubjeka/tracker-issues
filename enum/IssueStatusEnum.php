<?php

namespace tracker\enum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueStatusEnum extends BaseEnum
{
    const TYPE_DRAFT = 10;
    const TYPE_WORK = 30;
    const TYPE_FINISHED = 40;

    public static function getList()
    {
        return [
            self::TYPE_DRAFT => \Yii::t('TrackerIssuesModule.enum', 'Draft'),
            self::TYPE_WORK => \Yii::t('TrackerIssuesModule.enum', 'In work'),
            self::TYPE_FINISHED => \Yii::t('TrackerIssuesModule.enum', 'Finished'),
        ];
    }
}
