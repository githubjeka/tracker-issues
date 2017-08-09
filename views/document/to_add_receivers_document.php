<?php

use humhub\widgets\ActiveForm;

/**
 * @var $this \humhub\components\View
 * @var $actionUrl string
 * @var $requestModel \tracker\controllers\requests\DocumentRequest
 */
\tracker\assets\IssueAsset::register($this);
$this->registerJs(
    '$("#goto-document-form").hide();
    $(\'#user-picker-select\').on(\'change\', function() {       
        if ($(this).val()) {
            $("#goto-document-form").show();
        } else {
            $("#goto-document-form").hide();
        }
    })
    '
);
?>
<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('TrackerIssuesModule.views', 'Add Receivers'); ?>
            </h4>

        </div>

        <div class="modal-body">

            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'options' => ['class' => 'document-form'],
            ]); ?>

            <div class="form-group">
                <?= \humhub\modules\user\widgets\UserPickerField::widget([
                    'model' => $requestModel,
                    'attribute' => 'receivers',
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'Select receivers'),
                    'options' => ['autofocus' => true, 'id' => 'user-picker-select'],
                ]); ?>
                <p class="hint-block">
                    <?= Yii::t('TrackerIssuesModule.views',
                        'For each of receivers will be created new issue for familiarization with the document.') ?>
                </p>
            </div>

            <div class="form-group" id="goto-document-form">
                <button type="submit" class="btn btn-block btn-primary btn-sm"
                        data-ui-loader
                        data-action-click="tracker.getCreateForm"
                        data-action-url="<?= $actionUrl ?>"
                >
                    <?= Yii::t('TrackerIssuesModule.views', 'Add') ?>
                </button>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
