<?php

namespace tracker\permissions;

use humhub\modules\user\models\Group;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class AddReceiversToDocument extends \humhub\libs\BasePermission
{
    /**
     * @inheritdoc
     */
    protected $fixedGroups = [];

    /**
     * @inheritdoc
     */
    protected $defaultAllowedGroups = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->defaultAllowedGroups[] = Group::getAdminGroupId();

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Add receivers to documents');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Allow to added new receivers users to available documents');
    }

    public function getModuleId()
    {
        return Module::getIdentifier();
    }
}
