<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $documentRequest \tracker\controllers\requests\DocumentRequest */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="document-form">

    <?php $form = ActiveForm::begin(['id' => 'document-form']); ?>

    <?= $form->errorSummary($documentRequest); ?>

    <?= $form->field($documentRequest, 'file')->fileInput() ?>

    <?= $form->field($documentRequest, 'receivers')
        ->widget(\humhub\modules\user\widgets\UserPickerField::class,
            [
                'placeholder' => Yii::t('TrackerIssuesModule.views', 'Select receivers'),
                'options' => ['autofocus' => true],
            ]
        )->hint(Yii::t('TrackerIssuesModule.views',
            'For each of receivers will be created new issue for familiarization with the document.')); ?>

    <hr>

    <?= $form->field($documentRequest, 'category')
        ->dropDownList(\tracker\models\Document::categories(), ['prompt' => '-']); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($documentRequest, 'number')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <?= $form->field($documentRequest, 'registeredAt')
                    ->widget(yii\jui\DatePicker::className(), [
                        'dateFormat' => 'php:Y-m-d',
                        'clientOptions' => [],
                        'options' => [
                            'class' => 'form-control',
                            'placeholder' => Yii::t('TrackerIssuesModule.views', 'Date'),
                        ],
                    ]) ?>
            </div>
        </div>
    </div>

    <div>
        <?= $form->field($documentRequest, 'type')->dropDownList(\tracker\models\Document::types(),
            ['prompt' => '-']) ?>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($documentRequest, 'from')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($documentRequest, 'to')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <?= $form->field($documentRequest, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

    <div class="form-group">
        <?= humhub\widgets\RichtextField::widget([
            'placeholder' => Yii::t('TrackerIssuesModule.views', 'More details, please...'),
            'model' => $documentRequest,
            'attribute' => 'description',
            'label' => true,
            'options' => [
                'class' => 'atwho-input form-control humhub-ui-richtext issue-description-textarea',
            ],
        ]); ?>
    </div>

    <div class="form-group">
        <?= Html::button(
            Yii::t('TrackerIssuesModule.views', 'Save'),
            ['class' => 'btn btn-primary', 'onclick' => '$("#document-form").submit();']
        ) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
