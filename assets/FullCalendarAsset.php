<?php

namespace tracker\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class FullCalendarAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources/fullcalendar/';

    public $css = [
        'fullcalendar.min.css',
    ];

    public $js = [
        'lib/moment.min.js',
        'fullcalendar.min.js',
        'locale-all.js',
    ];

    public $depends = [
        JqueryAsset::class,
    ];
}
