<?php

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\modules\dashboard\widgets\DashboardContent;
use humhub\modules\space\widgets\Menu;
use humhub\modules\stream\widgets\StreamViewer;

return [
    'id' => 'tracker-issues',
    'class' => 'tracker\Module',
    'namespace' => 'tracker',
    'events' => [
        [
            'event' => Menu::EVENT_INIT,
            'class' => Menu::className(),
            'callback' => [\tracker\Events::class, 'onSpaceMenuInit'],
        ],
        [
            'event' => DashboardContent::EVENT_CREATE,
            'class' => DashboardContent::className(),
            'callback' => [\tracker\Events::class, 'onStreamViewerCreate'],
        ],
        [
            'event' => StreamViewer::EVENT_CREATE,
            'class' => StreamViewer::className(),
            'callback' => [\tracker\Events::class, 'onStreamViewerCreate'],
        ],
    ],
];
