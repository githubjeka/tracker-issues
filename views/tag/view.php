<?php
use yii\helpers\Html;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/* @var $this yii\web\View */
/* @var $model tracker\models\Tag */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \tracker\models\IssueSearch $searchModel */

?>
<div class="tag-view panel">
    <div class="panel-body">

        <p class="pull-right">
            <?= Html::a('<i class="fa fa-pencil"></i>', ['update', 'id' => $model->id],
                ['class' => 'btn btn-primary']) ?>
            <?= Html::a('<i class="fa fa-trash"></i>', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('TrackerIssuesModule.views', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>

        <h1>
            <?= \tracker\widgets\TagsWidget::widget(['tagsModels' => [$model]]) ?>
        </h1>

        <p><?= Html::encode($model->description) ?></p>

    </div>
</div>

<h3><?= Yii::t('TrackerIssuesModule.base', 'Tracker issues') ?></h3>

<div class="panel panel-default">
    <div class="panel-body">
        <?= $this->render('@tracker/views/issue/__gridView',
            [
                'hideTagsColumn' => true,
                'contentContainer' => false,
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]) ?>
    </div>
</div>
