<?php

namespace tracker\widgets;

use humhub\components\Widget;

class FinishIssueWidget extends Widget
{
    /**
     * Content Object
     */
    public $object;

    public function run()
    {
        return $this->render('finish_issue', [
            'object' => $this->object,
        ]);
    }
}
