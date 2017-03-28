<?php

namespace tracker\enum;

use humhub\modules\content\models\Content;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueVisibilityEnum extends BaseEnum
{
    const TYPE_PUBLIC = Content::VISIBILITY_PUBLIC;
    const TYPE_PROTECTED = Content::VISIBILITY_PRIVATE;
    const TYPE_PRIVATE = Content::VISIBILITY_OWNER;

    public static function getList()
    {
        return [
            self::TYPE_PUBLIC => \Yii::t('TrackerIssuesModule.enum', 'Public'),
            self::TYPE_PROTECTED => \Yii::t('TrackerIssuesModule.enum', 'Protected'),
            self::TYPE_PRIVATE => \Yii::t('TrackerIssuesModule.enum', 'Private'),
        ];
    }
}
