<?php

namespace tracker\assets;

use yii\web\AssetBundle;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class TimelineAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources/timeline/';

    public $css = [
        'timeline.css',
        'themes/timeline.humhub.css',
    ];

    public $js = [
        'timeline.js',
    ];

    public $depends = [
        IssueAsset::class,
    ];
}
