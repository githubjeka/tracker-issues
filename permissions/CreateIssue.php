<?php

namespace tracker\permissions;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class CreateIssue extends \humhub\libs\BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_FRIEND,
        User::USERGROUP_GUEST,
        User::USERGROUP_USER,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Create issue');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        if ($this->contentContainer instanceof User) {
            return \Yii::t('TrackerIssuesModule.permissions', 'Allow others to create new issue on your profile page');
        }
        return \Yii::t('TrackerIssuesModule.permissions', 'Allows the user to create issue');
    }

    public function getModuleId()
    {
        return Module::getIdentifier();
    }
}
