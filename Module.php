<?php

namespace tracker;

use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\space\models\Space;
use tracker\permissions\CreateIssue;
use tracker\permissions\EditIssue;

/**
 * Main class of module tracker issue
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Module extends ContentContainerModule
{
    public $id = 'tracker-issues';

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::className(),
        ];
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

        return [];
    }

    public static function getIdentifier()
    {
        return 'tracker-issues';
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
}
