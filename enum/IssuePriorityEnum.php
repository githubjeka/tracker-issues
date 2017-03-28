<?php

namespace tracker\enum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssuePriorityEnum extends BaseEnum
{
    const TYPE_URGENT = 130;
    const TYPE_CRITICAL = 120;
    const TYPE_SERIOUS = 110;
    const TYPE_NORMAL = 100;
    const TYPE_MINOR = 70;

    public static function getList()
    {
        return [
            self::TYPE_URGENT => \Yii::t('TrackerIssuesModule.enum', 'Urgent'),
            self::TYPE_CRITICAL => \Yii::t('TrackerIssuesModule.enum', 'Critical'),
            self::TYPE_SERIOUS => \Yii::t('TrackerIssuesModule.enum', 'Serious'),
            self::TYPE_NORMAL => \Yii::t('TrackerIssuesModule.enum', 'Normal'),
            self::TYPE_MINOR => \Yii::t('TrackerIssuesModule.enum', 'Minor'),
        ];
    }
}
