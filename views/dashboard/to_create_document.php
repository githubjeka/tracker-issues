<?php

use humhub\widgets\ActiveForm;
use yii\helpers\Html;

/**
 * @var $this \humhub\components\View
 * @var $actionUrl string
 */
\tracker\assets\IssueAsset::register($this);
$this->registerJs(
    '$("#goto-document-form").hide();
    $(\'#space-picker-select\').on(\'change\', function() {       
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
        <?= Yii::t('TrackerIssuesModule.views', 'New Document'); ?>
      </h4>

    </div>

    <div class="modal-body">

        <?php $form = ActiveForm::begin([
            'enableClientValidation' => false,
            'options' => ['class' => 'document-form'],
        ]); ?>

      <div class="form-group">
          <?= \humhub\modules\space\widgets\SpacePickerField::widget([
              'id' => 'space-picker-select',
              'name' => 'space',
              'focus' => true,
              'formName' => 'space',
              'minInput' => 2,
              'maxSelection' => 1,
          ]); ?>
        <p class="hint-block">
            <?= Yii::t('TrackerIssuesModule.views',
                'Only selected members from this space can get initial access to the viewing of the document in next step.'); ?>
        </p>
      </div>

        <?= Html::hiddenInput('u', \Yii::$app->user->identity->guid) ?>

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
