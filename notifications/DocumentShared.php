<?php

namespace tracker\notifications;

use humhub\modules\notification\components\BaseNotification;
use tracker\models\Document;
use tracker\Module;
use yii\helpers\Html;

/**
 * Class DocumentShared notifies receivers about documents
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentShared extends BaseNotification
{
    /**
     * @var Document
     */
    public $source;

    public function init()
    {
        $this->moduleId = Module::getIdentifier();
        parent::init();
    }

    /**
     * Note: this method rewrite parent because Document viewed not in content-container layout.
     * @inheritdoc
     * @return string
     */
    public function getUrl()
    {
        return \yii\helpers\Url::to([
            '/' . \tracker\Module::getIdentifier() . '/document/view',
            'id' => $this->source->id,
        ], true);
    }

    /**
     * @inheritdoc
     */
    public function html()
    {
        return \Yii::t(
            'TrackerIssuesModule.notify', '{userName} granted you access to the document "{document}"',
            [
                '{userName}' => '<strong>' . Html::encode($this->originator->getDisplayName()) . '</strong>',
                '{document}' => '"' . Html::encode($this->source->name) . '"',
            ]
        );
    }
}
