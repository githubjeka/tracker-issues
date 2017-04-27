<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model tracker\models\Tag */
/* @var $form yii\widgets\ActiveForm */

$containerId = time() . 'space-color-chooser-edit';
?>

<div class="tag-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'bg_color')->input('color') ?>

    <?= $form->field($model, 'text_color')->input('color') ?>

    <div class="form-group">
        <?= Html::submitButton(
            Yii::t('TrackerIssuesModule.views', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
