<?php

namespace tracker;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use tracker\permissions\AddDocument;
use tracker\permissions\AddReceiversToDocument;
use tracker\permissions\CreateIssue;
use tracker\permissions\EditIssue;
use tracker\permissions\GrandViewAllDocuments;
use tracker\permissions\ViewAllDocuments;
use yii\helpers\Url;

/**
 * Main class of module tracker issue
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Module extends ContentContainerModule
{
    public $id = 'tracker-issues';

    public $documentRootPath;
    /** @var bool */
    public $showOnTopMenu = true;

    public $resourcesPath = 'resources';

    public function init()
    {
        \Yii::setAlias('tracker', __DIR__);
        if (!is_dir($this->documentRootPath)) {
            $this->documentRootPath = \Yii::getAlias('@webroot/uploads/documents/');
        }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [Space::class, User::class];
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        if ($contentContainer !== null) {
            return [
                new CreateIssue(['contentContainer' => $contentContainer]),
                new EditIssue(['contentContainer' => $contentContainer]),
            ];
        }

        return [
            new AddDocument(),
            new AddReceiversToDocument(),
            new ViewAllDocuments(),
            new GrandViewAllDocuments(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function disable()
    {
        foreach (\tracker\models\Issue::find()->each() as $issue) {
            /** @var $issue \tracker\models\Issue */
            $issue->delete();
        }

        parent::disable();
    }

    public function getConfigUrl()
    {
        return Url::to(['/' . $this->getIdentifier() . '/config']);
    }

    public static function getIdentifier()
    {
        return 'tracker-issues';
    }
}
