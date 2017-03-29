<?php

namespace tracker\notifications;

use humhub\modules\notification\components\BaseNotification;
use tracker\Module;
use yii\helpers\Html;

/**
 * Class IssueEdited
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueEdited extends BaseNotification
{
    public function init()
    {
        $this->moduleId = Module::getIdentifier();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        /** @var \tracker\models\Issue $issue */
        $issue = $this->source;
        return \Yii::t(
            'TrackerIssuesModule.views', '{userName} changed the issue {issue} wherever you are assigned.',
            [
                '{userName}' => '<strong>' . Html::encode($this->originator->getDisplayName()) . '</strong>',
                '{issue}' => '"' . Html::encode($issue->getContentDescription()) . '"',
            ]
        );
    }
}
