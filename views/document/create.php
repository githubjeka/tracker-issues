<?php

/* @var $this yii\web\View */
/* @var $documentRequest \tracker\controllers\requests\DocumentRequest */

$this->title = Yii::t('TrackerIssuesModule.views', 'Create Document');
?>

<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('TrackerIssuesModule.views', 'Create Document') ?>
            </h4>
        </div>

        <div class="modal-body">
            <?= $this->render('_form', ['documentRequest' => $documentRequest]) ?>
        </div>
    </div>
</div>
