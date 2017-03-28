<?php

namespace tracker\assets;

use yii\web\AssetBundle;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueAsset extends AssetBundle
{
    public $sourcePath = __DIR__;

    public $css = [
        'main.css',
    ];

    public $js = [
        'humhub.issue.js',
    ];

    public $depends = [
        'humhub\assets\CoreApiAsset',
    ];

}
