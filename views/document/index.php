<?php

use tracker\models\Document;
use tracker\permissions\AddDocument;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel tracker\models\DocumentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
\tracker\assets\IssueAsset::register($this);
$this->title = Yii::t('TrackerIssuesModule.views', 'Documents');
?>
<style>
    #filters-form-document .form-group {
        margin-bottom: 0;
    }
</style>
<div class="panel panel-default">
    <div class="panel-body">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'enableClientValidation' => false,
            'method' => 'get',
            'id'=>'filters-form-document'
        ]); ?>

        <div id="search-form-more" class="content collapse">

            <?= $form
                ->field($searchModel, 'number')
                ->label(false)
                ->textInput([
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'Filter by Number'),
                    'class' => 'input-sm form-control',
                ]) ?>

            <?= $form
                ->field($searchModel, 'type')
                ->label(false)
                ->dropDownList(Document::types(), ['prompt' => Yii::t('TrackerIssuesModule.views', 'Filter by Type'), 'class' => 'input-sm form-control']) ?>

            <?= $form
                ->field($searchModel, 'from')
                ->label(false)
                ->textInput([
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'Filter by From'),
                    'class' => 'input-sm form-control',
                ]) ?>

            <?= $form
                ->field($searchModel, 'to')
                ->label(false)
                ->textInput([
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'Filter by To'),
                    'class' => 'input-sm form-control',
                ]) ?>
        </div>

        <div class="text-center">
            <p>
                <a role="button" data-toggle="collapse" class="dropdown-toggle btn btn-sm btn-default"
                   href="#search-form-more" aria-expanded="false"
                   aria-controls="search-form-fields">
                    <small><?= Yii::t('TrackerIssuesModule.views', 'More filters') ?></small>
                </a>
            </p>
        </div>

        <?= $form->field($searchModel, 'category')
            ->label(false)
            ->dropDownList(Document::categories(), [
                'prompt' => Yii::t('TrackerIssuesModule.views', 'Filter by Category'),
                'class' => 'input-sm form-control',
            ]) ?>

        <?= $form
            ->field($searchModel, 'name')
            ->label(false)
            ->textInput([
                'placeholder' => Yii::t('TrackerIssuesModule.views', 'Filter by Name'),
                'class' => 'input-lg form-control',
            ]) ?>

        <div class="text-center">
            <button class="btn btn-success btn-xs text-uppercase">
                <?= Yii::t('TrackerIssuesModule.views',                    'Search') ?>
            </button>
        </div>

        <?php ActiveForm::end(); ?>

        <?php if (\Yii::$app->user->can(new AddDocument())) : ?>
            <p class="pull-left">
                <?php $url = Url::to(['/' . tracker\Module::getIdentifier() . '/document/create']); ?>
                <a href="<?= $url; ?>" class="btn btn-primary btn-sm" data-target="#globalModal">
                    <i class="fa fa-plus"></i> <?= Yii::t('TrackerIssuesModule.views', 'New Document'); ?>
                </a>
            </p>
        <?php endif; ?>

    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <?= \yii\widgets\ListView::widget([
            'dataProvider' => $dataProvider,
            'summary' => Yii::t(
                'TrackerIssuesModule.views',
                'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{document} other{documents}}.'
            ),
            'itemView' => '_item.php',
        ]) ?>
    </div>
</div>
