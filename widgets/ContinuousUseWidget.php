<?php

namespace tracker\widgets;

use yii\helpers\Html;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class ContinuousUseWidget extends \yii\bootstrap\Widget
{
    public $show = false;

    /**
     * @return string
     */
    public function run()
    {
        if ($this->show) {
            return Html::tag(
                'span',
                \Yii::t('TrackerIssuesModule.views', 'Continuous use'),
                ['class' => 'label label-default']);
        }

        return '';
    }
}
