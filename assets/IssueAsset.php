<?php

namespace tracker\assets;

use yii\web\AssetBundle;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/../resources/common/';

    public $css = [
        'css/main.css',
    ];

    public $js = [
        'js/humhub.tracker.js',
    ];

    public $depends = [
        '\humhub\modules\stream\assets\StreamAsset',
        '\humhub\assets\CoreApiAsset',
    ];

}
