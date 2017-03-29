<?php

namespace tracker\notifications;

use humhub\modules\notification\components\BaseNotification;
use tracker\Module;
use yii\helpers\Html;

/**
 * Class Assigned
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Assigned extends BaseNotification
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
            'TrackerIssuesModule.views', '{userName} assigned you to the issue {issue}.',
            [
                '{userName}' => '<strong>' . Html::encode($this->originator->getDisplayName()) . '</strong>',
                '{issue}' => '"' . Html::encode($issue->getContentDescription()) . '"',
            ]
        );
    }
}
