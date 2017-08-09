<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model tracker\models\Document */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Document',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Documents'), 'url' => ['index']];
?>
<div class="document-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
