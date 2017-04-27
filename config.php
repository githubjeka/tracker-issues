<?php

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\modules\content\widgets\WallEntryLinks;
use humhub\modules\space\widgets\Menu;
use humhub\modules\stream\widgets\StreamViewer;
use humhub\widgets\TopMenu;

return [
    'id' => 'tracker-issues',
    'class' => 'tracker\Module',
    'namespace' => 'tracker',
    'events' => [
        [
            'class' => Menu::class,
            'event' => Menu::EVENT_INIT,
            'callback' => [\tracker\Events::class, 'onSpaceMenuInit'],
        ],
        [
            'class' => StreamViewer::class,
            'event' => StreamViewer::EVENT_CREATE,
            'callback' => [\tracker\Events::class, 'onStreamViewerCreate'],
        ],
        [
            'class' => TopMenu::class,
            'event' => TopMenu::EVENT_INIT,
            'callback' => [\tracker\Events::class, 'onTopMenuInit'],
        ],
        [
            'class' => WallEntryLinks::className(),
            'event' => WallEntryLinks::EVENT_INIT,
            'callback' => [\tracker\Events::class, 'onWallEntryAddonInit'],
        ],
    ],
];
