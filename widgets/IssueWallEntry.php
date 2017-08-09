<?php

namespace tracker\widgets;

use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueWallEntry extends \humhub\modules\content\widgets\WallEntry
{
    public $wallEntryLayout = 'wallEntry';

    public function run()
    {
        \Yii::$app->response->on(\yii\web\Response::EVENT_AFTER_SEND, function () {
            \Yii::$app->session->remove('parents');
            \Yii::$app->session->remove('childs');
        });

        return $this->renderFile(__DIR__ . '/../views/issue/view.php', ['issue' => $this->contentObject,]);
    }

    public function getEditUrl()
    {
        $this->editRoute = '/' . Module::getIdentifier() . '/issue/edit';
        return parent::getEditUrl();
    }
}
