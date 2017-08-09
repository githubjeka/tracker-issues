<?php

namespace tracker\permissions;

use humhub\modules\user\models\Group;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class AddDocument extends \humhub\libs\BasePermission
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
        return \Yii::t('TrackerIssuesModule.permissions', 'Add documents');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Allow to upload new documents and to add information on it');
    }

    public function getModuleId()
    {
        return Module::getIdentifier();
    }
}
