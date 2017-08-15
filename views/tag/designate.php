<?php

use yii\helpers\Url;

/**
 * @var $formModel \tracker\controllers\IssueRequest
 */
?>
<div class="modal-dialog modal-dialog-normal animated fadeIn">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

            <h4 class="modal-title" id="myModalLabel">
                <?= Yii::t('TrackerIssuesModule.views', '<strong>Designate</strong> tag to issue'); ?>
            </h4>

        </div>

        <div class="modal-body">

            <?php $form = \humhub\widgets\ActiveForm::begin() ?>

            <?=
            $form->field($formModel, 'tags')
                ->dropDownList(
                    \yii\helpers\ArrayHelper::map(
                        \tracker\models\Tag::find()
                            ->byUser(Yii::$app->user->id)
                            ->orderBy([\tracker\models\Tag::tableName() . '.name' => SORT_ASC])
                            ->all(),
                        'id', 'name'
                    ),
                    ['text' => 'Please select', 'multiple' => true, 'class' => 'select-form']
                );
            ?>

            <button type="submit" class="btn btn-block btn-primary btn-sm"
                    data-action-submit
                    data-action-click="tracker.designateTag"
                    data-action-url="<?= Url::to([
                        '/' . \tracker\Module::getIdentifier() . '/tag/designate',
                        'id' => $formModel->id,
                    ]) ?>"
            >
                <?= Yii::t('TrackerIssuesModule.views', 'Save') ?>
            </button>

            <?php \humhub\widgets\ActiveForm::end(); ?>
        </div>
    </div>
</div>

