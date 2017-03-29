<?php

namespace tracker\notifications;

use humhub\modules\notification\components\BaseNotification;
use tracker\Module;
use yii\helpers\Html;

class Assigned extends BaseNotification
{
    public $viewName = "assigned";

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
        return \Yii::t(
            'TrackerIssuesModule.views', '{userName} assigned you to the issue {issue}.',
            [
                '{userName}' => '<strong>' . Html::encode($this->originator->getDisplayName()) . '</strong>',
                '{issue}' => '"' . Html::encode($this->source->getContentDescription()) . '"',
            ]
        );
    }
}
