<?php

namespace tracker;

use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Menu;
use tracker\models\Issue;
use yii\base\Event;

/**
 * Description of TrackerEvents
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Events extends \yii\base\Object
{
    public static function onSpaceMenuInit(Event $event)
    {
        /** @var Menu $sender */
        $sender = $event->sender;
        if (!($sender instanceof Menu)) {
            throw new \LogicException();
        }

        /** @var Space $space */
        $space = $sender->space;
        if (!($space instanceof Space)) {
            throw new \LogicException();
        }

        if ($space->isModuleEnabled('tracker-issues') && $space->isMember()) {
            $sender->addItem([
                'label' => \Yii::t('TrackerIssuesModule.base', 'Tracker issues'),
                'group' => 'modules',
                'url' => $space->createUrl("/tracker-issues/issue/show"),
                'icon' => '<i class="fa fa-check-square"></i>',
                'isActive' => (\Yii::$app->controller->module->id === 'tracker-issues'),
            ]);
        }
    }

    /**
     * NOTE: It's may doesn't worked if other modules uses it too. Issue https://github.com/humhub/humhub/issues/2412
     *
     * @param Event $event
     */
    public static function onStreamViewerCreate(Event $event)
    {
        if (isset($event->config['contentContainer'])) {
            if ($event->config['contentContainer']->isModuleEnabled('tracker-issues')) {
                $event->config['streamAction'] = '/tracker-issues/issue/stream';
            }
        } elseif (isset($event->config['streamAction'])) {
            $event->config['streamAction'] = '/tracker-issues/dashboard/stream';
        }
    }

    public static function onTopMenuInit(Event $event)
    {
        $user = \Yii::$app->user;

        if ($user->isGuest) {
            return;
        }

        $controller = \Yii::$app->controller;
        $module = $controller->module;

        $event->sender->addItem([
            'label' => \Yii::t('TrackerIssuesModule.base', 'Tracker issues'),
            'url' => ['/tracker-issues/dashboard/issues'],
            'icon' => '<i class="fa fa-tasks"></i>',
            'isActive' => ($module && $module->id === 'tracker-issues' && $controller->id === 'dashboard' &&
                           $controller->action->id === 'issues'),
            'sortOrder' => 300,
        ]);

        $event->sender->addItem([
            'label' => \Yii::t('TrackerIssuesModule.views', 'Documents'),
            'url' => ['/tracker-issues/document'],
            'icon' => '<i class="fa fa-files-o"></i>',
            'isActive' => ($module && $module->id === 'tracker-issues' && $controller->id === 'document'),
            'sortOrder' => 300,
        ]);

        $event->sender->addItem([
            'label' => \Yii::t('TrackerIssuesModule.views', 'Tags'),
            'url' => ['/tracker-issues/tag/index'],
            'icon' => '<i class="fa fa-tags"></i>',
            'isActive' => ($module && $module->id === 'tracker-issues' && $controller->id === 'tag'),
            'sortOrder' => 300,
        ]);
    }

    /**
     * On init of the WallEntryAddonWidget, attach the tags widget.
     *
     * @param Event $event
     */
    public static function onWallEntryAddonInit(Event $event)
    {
        if ($event->sender->object instanceof Issue) {
            $issue = $event->sender->object;
            $event->sender
                ->addWidget(
                    widgets\DesignateTagWidget::className(),
                    ['object' => $issue],
                    ['sortOrder' => 30]
                );
            $event->sender
                ->addWidget(
                    widgets\SubtaskWidget::className(),
                    ['object' => $issue],
                    ['sortOrder' => 30]
                );
            if ($issue->status != \tracker\enum\IssueStatusEnum::TYPE_FINISHED) {
                $event->sender
                    ->addWidget(
                        widgets\FinishIssueWidget::className(),
                        ['object' => $issue],
                        ['sortOrder' => 30]
                    );
            }
        }
    }
}
