<?php

namespace tracker\widgets;


class StreamViewer extends \humhub\modules\stream\widgets\StreamViewer
{
    public function beforeRun()
    {
        if (!parent::beforeRun()) {
            return false;
        }

        $contentId = \Yii::$app->request->getQueryParam('contentId');

        if (!empty($contentId)) {
            $this->view = 'wallStreamIssue';
            $this->showFilters = false;
        }

        return true;
    }
}