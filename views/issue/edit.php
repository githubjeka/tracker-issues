<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \tracker\controllers\IssueRequest $issueForm
 */
use yii\bootstrap\ActiveForm;

?>

<?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

  <h4>
      <?= Yii::t('TrackerIssuesModule.views', '<strong>Edit</strong> issue'); ?>
  </h4>

  <div class="body">
      <?= $this->render('form', ['issueForm' => $issueForm, 'submitAjax' => true]) ?>
  </div>

<?php ActiveForm::end(); ?>
