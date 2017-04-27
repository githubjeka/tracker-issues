<?php

namespace tracker\widgets;


use humhub\components\Widget;

class DesignateTagWidget extends Widget
{
    /**
     * Content Object
     */
    public $object;

    public function run()
    {
        return $this->render('designate_tags', [
            'object' => $this->object,
        ]);
    }
}
