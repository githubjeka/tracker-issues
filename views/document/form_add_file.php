<?php

use humhub\widgets\ActiveForm;

/**
 * @var $this \humhub\components\View
 * @var $actionUrl string
 * @var $requestModel \tracker\controllers\requests\DocumentRequest
 */
\tracker\assets\IssueAsset::register($this);
$this->registerJs(
    '$("#submit-block").hide();
    $(\'#documentrequest-file\').on(\'change\', function() {       
        if ($(this).val()) {
            $("#submit-block").show();
        } else {
            $("#submit-block").hide();
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
                <?= Yii::t('TrackerIssuesModule.views', 'Change file'); ?>
            </h4>

        </div>

        <div class="modal-body">

            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'options' => ['class' => 'document-form']
            ]); ?>

            <div class="form-group">
                <?= $form->field($requestModel, 'file')->fileInput() ?>
            </div>

            <div class="form-group" id="submit-block">
                <button type="submit" class="btn btn-block btn-primary btn-sm">
                    <?= Yii::t('TrackerIssuesModule.views', 'Add') ?>
                </button>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
