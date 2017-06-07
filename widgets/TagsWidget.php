<?php

namespace tracker\widgets;

use humhub\components\Widget;
use tracker\Module;
use yii\bootstrap\Html;

class TagsWidget extends Widget
{
    public $tagsModels = [];
    public $asLink = false;

    public function run()
    {
        $html = '';
        foreach ($this->tagsModels as $tagModel) {

            $style = 'background-color:' . $tagModel->bg_color . '!important;color:' . $tagModel->text_color .
                     '!important;';

            if ($this->asLink) {
                $html .= Html::a(
                    '<span class="label label-default" style="' . $style . '">' . Html::encode($tagModel->name),
                    ['/' . Module::getIdentifier() . '/tag/view', 'id' => $tagModel->id]
                );
            } else {

                $html .= '<span class="label label-default" style="' . $style . '">';
                $html .= Html::encode($tagModel->name);

            }

            $html .= '</span> ';
        }
        return $html;
    }
}
