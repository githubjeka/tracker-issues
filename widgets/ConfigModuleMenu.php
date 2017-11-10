<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\widgets;


use humhub\widgets\SettingsTabs;
use tracker\Module;
use Yii;
use yii\helpers\Url;

class ConfigModuleMenu extends SettingsTabs
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->items = [
            [
                'label' => Yii::t('TrackerIssuesModule.views', 'Categories of documents'),
                'url' => Url::toRoute(['/' . Module::getIdentifier() . '/config/categories']),
                'active' => $this->isCurrentRoute(Module::getIdentifier(), 'config', 'categories'),
                'sortOrder' => 10
            ],
            [
                'label' => Yii::t('TrackerIssuesModule.views', 'Types of documents'),
                'url' => Url::toRoute(['/' . Module::getIdentifier() . '/config/types']),
                'active' => $this->isCurrentRoute(Module::getIdentifier(), 'config', 'types'),
                'sortOrder' => 20
            ],
        ];

        parent::init();
    }

}
