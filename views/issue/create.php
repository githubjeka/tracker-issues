<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \tracker\controllers\requests\IssueRequest $issueForm
 */
?>

<div class="modal-dialog modal-dialog-normal animated fadeIn">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

      <h4 class="modal-title" id="myModalLabel">
          <?= Yii::t('TrackerIssuesModule.views', 'New issue'); ?>
      </h4>

    </div>

    <div class="modal-body">
        <?= $this->render('form', ['issueForm' => $issueForm]) ?>
    </div>
  </div>
</div>
