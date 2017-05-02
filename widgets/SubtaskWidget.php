<?php

namespace tracker\widgets;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
use humhub\components\Widget;

class SubtaskWidget extends Widget
{
    /**
     * Content Object
     */
    public $object;

    public function run()
    {
        return $this->render('create_subtask', [
            'object' => $this->object,
        ]);
    }
}
