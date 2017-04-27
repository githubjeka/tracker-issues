<?php

use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel tracker\models\TagSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('TrackerIssuesModule.views', 'Tags');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-index panel">
    <div class="panel-heading">
        <h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
    </div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-3">
                <?= Html::a(Yii::t('TrackerIssuesModule.views', 'Create Tag'), ['create'],
                    ['class' => 'btn btn-success btn-lg']) ?>
            </div>
            <div class="col-md-9">
                <div class="alert alert-info">
                    <?= Yii::t('TrackerIssuesModule.views',
                        'This list of personal tags. It is can be a tag, a milestone, label or other that you can mark a issue. This tags visible only for you.') ?>
                </div>
            </div>
        </div>

        <?php Pjax::begin(); ?>

        <?= \humhub\widgets\GridView::widget([
            'options' => ['class' => 'table-responsive'],
            'tableOptions' => ['class' => 'table table-condensed'],
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'tag',
                    'header' => \Yii::t('TrackerIssuesModule.views', 'Name'),
                    'format' => 'raw',
                    'value' => function ($model) {
                        return \tracker\widgets\TagsWidget::widget(['tagsModels' => [$model], 'asLink' => true]);
                    },
                ],
                [
                    'attribute' => 'description',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                ],
            ],
        ]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>
