<?php

namespace tracker\permissions;

use humhub\modules\user\models\Group;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class ViewAllDocuments extends \humhub\libs\BasePermission
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
        return \Yii::t('TrackerIssuesModule.permissions', 'View all documents');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return \Yii::t('TrackerIssuesModule.permissions', 'Allow to view all documents and their all issues');
    }

    public function getModuleId()
    {
        return Module::getIdentifier();
    }
}
