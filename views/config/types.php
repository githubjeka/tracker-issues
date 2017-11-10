<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var $createUrl string
 * @var $dataProvider \yii\data\ActiveDataProvider
 */

use humhub\widgets\ModalButton;
use yii\widgets\ListView;

?>

<div class="panel panel-default">

    <div class="panel-heading">
        <strong><?= Yii::t('TrackerIssuesModule.base', 'Tracker issues'); ?>:</strong>
        <?= Yii::t('TrackerIssuesModule.views', 'Types of documents'); ?>
    </div>

    <?= \tracker\widgets\ConfigModuleMenu::widget() ?>

    <div class="panel-body">

        <h4>
            <?= Yii::t('TrackerIssuesModule.views', 'Configuration for types of documents'); ?>
        </h4>

        <div class="help-block">
            <?= Yii::t(
                'TrackerIssuesModule.views',
                'Here you must manage your types of documents and their colors. After, you can found theirs in edit form of a document.'
            ) ?>
        </div>

        <?= ModalButton::success(Yii::t('TrackerIssuesModule.views', 'Add new type'))
            ->load(['/' . \tracker\Module::getIdentifier() . '/config/create-type'])
            ->icon('fa-plus')
            ->options(['class' => 'btn-block text-uppercase']); ?>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_typeItem',
            'emptyText' => Yii::t(
                'TrackerIssuesModule.views',
                'There are currently no document types available.'
            )
        ]) ?>
    </div>
</div>
