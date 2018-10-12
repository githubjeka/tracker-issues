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
            'class' => \humhub\modules\space\controllers\SpaceController::class,
            'event' => \humhub\modules\space\controllers\SpaceController::EVENT_BEFORE_ACTION,
            'callback' => [\tracker\Events::class, 'onBeforeSpaceActions'],
        ],
        [
            'class' => \humhub\modules\user\controllers\ProfileController::class,
            'event' => \humhub\modules\user\controllers\ProfileController::EVENT_BEFORE_ACTION,
            'callback' => [\tracker\Events::class, 'onBeforeSpaceActions'],
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
