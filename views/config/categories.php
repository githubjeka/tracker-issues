<?php
/* @var $this yii\web\View */

use humhub\widgets\ModalButton;
use yii\widgets\ListView;

?>

<div class="panel panel-default">

    <div class="panel-heading">
        <strong><?= Yii::t('TrackerIssuesModule.base', 'Tracker issues'); ?>:</strong>
        <?= Yii::t('TrackerIssuesModule.views', 'Categories of documents'); ?>
    </div>

    <?= \tracker\widgets\ConfigModuleMenu::widget() ?>

    <div class="panel-body">
        <div class="row">
            <div class="col-lg-8">
                <h4>
                    <?= Yii::t('TrackerIssuesModule.views', 'Configuration for categories of documents'); ?>
                </h4>

                <div class="help-block">
                    <?= Yii::t(
                        'TrackerIssuesModule.views',
                        'Here you must manage your categories of documents and their text colors. After, you can found theirs in edit form of a document.'
                    ) ?>
                </div>
            </div>
            <div class="col-lg-4">
                <?= ModalButton::success(Yii::t('TrackerIssuesModule.views', 'Add new category'))
                    ->load(['/' . \tracker\Module::getIdentifier() . '/config/create-category'])
                    ->icon('fa-plus')
                    ->options(['class' => 'btn-block text-uppercase']); ?>
            </div>
        </div>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'itemView' => '_categoryItem',
            'emptyText' => Yii::t(
                'TrackerIssuesModule.views',
                'There are currently no document categories available.'
            )
        ]) ?>
    </div>
</div>
