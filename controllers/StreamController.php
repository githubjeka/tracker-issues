<?php

namespace tracker\controllers;

use humhub\modules\content\components\ContentContainerController;
use tracker\controllers\actions\StreamAction;

class StreamController extends ContentContainerController
{
    /**
     * Returns layout view of Stream
     */
    public function actionIndex()
    {
        $this->subLayout = '@tracker/views/layouts/sub_layout_issues';
        return $this->render('layout', [
            'contentContainer' => $this->contentContainer,
        ]);
    }

    /**
     * Returns JSON format for content of stream
     */
    public function actionView()
    {
        $action = new StreamAction($this->action->id, $this, [
            'contentContainer' => $this->contentContainer,
        ]);

        return $action->run();
    }
}