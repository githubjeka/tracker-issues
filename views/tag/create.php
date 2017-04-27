<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model tracker\models\Tag */

$this->title = Yii::t('TrackerIssuesModule.views', 'Create Tag');
$this->params['breadcrumbs'][] = ['label' => Yii::t('TrackerIssuesModule.views', 'Tags'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tag-create panel">
    <div class="panel-body">
        <h1><?= Html::encode($this->title) ?></h1>

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
