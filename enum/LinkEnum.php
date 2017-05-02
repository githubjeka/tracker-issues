<?php

namespace tracker\enum;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * NOTE: Constants must be integer
 * @see m170502_062820_tracker_links::up() type field
 */
class LinkEnum extends BaseEnum
{
    const TYPE_REFER = 10;
    const TYPE_SUBTASK = 20;

    public static function getList()
    {
        return [
            self::TYPE_REFER => \Yii::t('TrackerIssuesModule.enum', 'Refer'),
            self::TYPE_SUBTASK => \Yii::t('TrackerIssuesModule.enum', 'Subtask'),
        ];
    }
}
