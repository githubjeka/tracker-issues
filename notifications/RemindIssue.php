<?php

namespace tracker\notifications;

use humhub\modules\notification\components\BaseNotification;
use humhub\modules\user\models\User;
use tracker\Module;
use yii\helpers\Html;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class RemindIssue extends BaseNotification
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
            'TrackerIssuesModule.views', '{userName} reminds you of the issue. {issue}',
            [
                '{userName}' => '<strong>' . Html::encode($this->originator->getDisplayName()) . '</strong>',
                '{issue}' => '"' . Html::encode($issue->getContentDescription()) . '"',
            ]
        );
    }


    /**
     * Marks notification as seen
     */
    public function markAsSeen()
    {
        parent::markAsSeen();
        $notification = new RemindIssueSeen();
        $notification->source = $this->source;
        $notification->originator = User::findOne($this->record->user_id);
        $notification->send($this->originator);
    }
}
