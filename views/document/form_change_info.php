<?php

use humhub\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this \humhub\components\View
 * @var $actionUrl string
 * @var $requestModel \tracker\controllers\requests\DocumentRequest
 */
?>
<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('TrackerIssuesModule.views', 'Change info'); ?>
            </h4>

        </div>

        <div class="modal-body">

            <div class="document-form">

                <?php $form = ActiveForm::begin(['id' => 'document-form']); ?>

                <?= $form->errorSummary($requestModel); ?>

                <?= $form->field($requestModel, 'category')
                    ->dropDownList(\tracker\models\Document::categories(), ['prompt' => '-']); ?>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($requestModel, 'number')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <?= $form->field($requestModel, 'registeredAt')
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
                    <?= $form->field($requestModel, 'type')->dropDownList(\tracker\models\Document::types(),
                        ['prompt' => '-']) ?>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($requestModel, 'from')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($requestModel, 'to')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>

                <?= $form->field($requestModel, 'name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>

                <div class="form-group">
                    <?= humhub\widgets\RichtextField::widget([
                        'placeholder' => Yii::t('TrackerIssuesModule.views', 'More details, please...'),
                        'model' => $requestModel,
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


        </div>
    </div>
</div>
