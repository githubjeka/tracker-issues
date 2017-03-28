<?php

namespace tracker;

use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Menu;

/**
 * Description of TrackerEvents
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class Events extends \yii\base\Object
{
    public static function onSpaceMenuInit(\yii\base\Event $event)
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

    public static function onStreamViewerCreate(\yii\base\Event $event)
    {
        if (isset($event->config['contentContainer'])) {
            if ($event->config['contentContainer']->isModuleEnabled('tracker-issues')) {
                $event->config['streamAction'] = '/tracker-issues/issue/stream';
            }
        } elseif (isset($event->config['streamAction'])) {
            $event->config['streamAction'] = '/tracker-issues/dashboard/stream';
        }
    }
}
