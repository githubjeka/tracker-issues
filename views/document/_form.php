<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var $this yii\web\View
 * @var $documentRequest \tracker\controllers\requests\DocumentRequest
 * @var $form yii\widgets\ActiveForm
 */
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

    <?= $form->field($documentRequest, 'space')
        ->widget(\humhub\modules\space\widgets\SpacePickerField::class,
            [
                'placeholder' => Yii::t('TrackerIssuesModule.views', 'Select space'),
                'maxSelection' => 1,
                'options' => ['autofocus' => false],
            ]
        )->hint(Yii::t('TrackerIssuesModule.views',
            'For each of members from this Space will be created new issue for familiarization with the document.')); ?>

    <hr>

    <?= $form->field($documentRequest, 'category')
        ->dropDownList(
            \yii\helpers\ArrayHelper::map(\tracker\models\Document::categories(), 'id', 'name'),
            ['prompt' => '-']
        ); ?>

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
        <?= $form->field($documentRequest, 'type')
            ->dropDownList(
                \yii\helpers\ArrayHelper::map(\tracker\models\Document::types(), 'id', 'name'),
                ['prompt' => '-']
            ) ?>
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
        <?= \humhub\modules\content\widgets\richtext\RichTextField::widget([
            'placeholder' => Yii::t('TrackerIssuesModule.views', 'More details, please...'),
            'model' => $documentRequest,
            'attribute' => 'description',
            'label' => true,
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
