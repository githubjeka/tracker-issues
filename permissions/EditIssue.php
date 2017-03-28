<?php

namespace tracker\permissions;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class EditIssue extends \humhub\libs\BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_MEMBER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Edit issue');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        if ($this->contentContainer instanceof User) {
            return \Yii::t('TrackerIssuesModule.permissions', 'Allow others to edit issue on your profile page');
        }
        return \Yii::t('TrackerIssuesModule.permissions', 'Allows the user to edit issue');
    }

    public function getModuleId()
    {
        return Module::getIdentifier();
    }
}
