<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \tracker\controllers\requests\IssueRequest $issueForm
 * @var boolean $submitAjax
 */

use tracker\models\Issue;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

if (!isset($submitAjax)) {
    $submitAjax = false;
}

$isSpace = $this->context->contentContainer instanceof \humhub\modules\space\models\Space;
?>

<?php $form = ActiveForm::begin(['enableClientValidation' => false, 'options' => ['class' => 'issue-form']]); ?>

<?= Html::hiddenInput(Html::getInputName($issueForm, 'id'), $issueForm->id) ?>

<?php if ($issueForm->status === \tracker\enum\IssueStatusEnum::TYPE_FINISHED) : ?>
    <?= $form->field($issueForm, 'cancelFinish')->checkbox(); ?>
<?php else : ?>
    <div class="row">
        <div class="col-md-8">

            <?= $form->field($issueForm, 'title')
                ->textInput([
                    'id' => 'itemTask',
                    'class' => 'form-control input-lg',
                    'maxlength' => true,
                    'placeholder' => Yii::t('TrackerIssuesModule.views', 'What is to do?'),
                ]); ?>

            <?= $form->field($issueForm, 'description')
                ->widget(
                    \humhub\modules\content\widgets\richtext\RichTextField::class,
                    [
                        'id' => 'issue_description_' . $issueForm->id,
                        'placeholder' => Yii::t('TrackerIssuesModule.views', 'More details, please...'),
                    ])
                ->label(true) ?>

            <hr>

            <div class="row">
                <div class="col-md-6">

                    <div>
                        <strong><?= Yii::t('TrackerIssuesModule.views', 'Started Date') ?></strong>
                        <small class="help-block">
                            <?= Yii::t('TrackerIssuesModule.views', 'From this time recommended begin to start work'); ?>
                        </small>
                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($issueForm, 'constantly')->checkbox() ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?= $form->field($issueForm, 'startedDate')
                                ->widget(yii\jui\DatePicker::className(), [
                                    'dateFormat' => 'php:Y-m-d',
                                    'clientOptions' => [],
                                    'options' => [
                                        'style'=>'position: relative; z-index: 17;',
                                        'class' => 'form-control',
                                        'placeholder' => Yii::t('TrackerIssuesModule.views', 'Date'),
                                    ],
                                ])->label(false); ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($issueForm, 'startedTime')->input('time')->label(false); ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div>
                        <strong><?= Yii::t('TrackerIssuesModule.views', 'Deadline') ?></strong>
                        <small class="help-block">
                            <?= Yii::t('TrackerIssuesModule.views', 'The planned time by which you should end work.'); ?>
                        </small>

                        <button type="button" class="btn btn-link btn-sm"
                                onclick="$('#issuerequest-deadlinedate').datepicker('setDate', null);$('#issuerequest-deadlinetime').val('');">
                            <?= Yii::t('TrackerIssuesModule.views', 'Has not deadline') ?>
                        </button>

                        <hr>
                        <div class="form-group">
                            <?= $form->field($issueForm, 'deadlineDate')
                                ->widget(yii\jui\DatePicker::className(), [
                                    'dateFormat' => 'php:Y-m-d',
                                    'clientOptions' => [],
                                    'options' => [
                                        'style'=>'position: relative; z-index: 17;',
                                        'class' => 'form-control',
                                        'placeholder' => Yii::t('TrackerIssuesModule.views', 'Date'),
                                    ],
                                ])->label(false); ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($issueForm, 'deadlineTime')->input('time')->label(false); ?>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <div class="col-md-4">

            <?= $form->field($issueForm, 'visibility')
                ->dropDownList(\tracker\enum\ContentVisibilityEnum::getList()); ?>

            <?= $form->field($issueForm, 'priority')
                ->dropDownList(\tracker\enum\IssuePriorityEnum::getList()); ?>

            <?php if ($isSpace) : ?>
                <?= $form->field($issueForm, 'assignedUsers')
                    ->widget(\humhub\modules\user\widgets\UserPickerField::class,
                        [
                            'url' => $this->context->contentContainer->createUrl('/space/membership/search'),
                            'placeholder' => Yii::t('TrackerIssuesModule.views', 'Select assignees'),
                        ]
                    ); ?>
            <?php endif; ?>

            <hr>

            <h4><?= Yii::t('AdminModule.base', 'Files') ?></h4>

            <div class="row">
                <div class="col-md-2">
                    <div id="post_upload_progress_<?= $issueForm->id ?>" style="display:none;margin:10px 0px;"></div>
                    <?=
                    \humhub\modules\file\widgets\UploadButton::widget([
                        'id' => 'post_upload_' . $issueForm->id,
                        'model' => new Issue,
                        'dropZone' => '#post_edit_' . $issueForm->id . ':parent',
                        'preview' => '#post_upload_preview_' . $issueForm->id,
                        'progress' => '#post_upload_progress_' . $issueForm->id,
                        'max' => Yii::$app->getModule('content')->maxAttachedFiles,
                    ])
                    ?>
                </div>
                <div class="col-md-10">
                    <?=
                    \humhub\modules\file\widgets\FilePreview::widget([
                        'id' => 'post_upload_preview_' . $issueForm->id,
                        'options' => ['style' => 'margin-top:10px'],
                        'model' => ($issue = Issue::findOne($issueForm->id)) !== null ? $issue : new Issue,
                        'edit' => true,
                    ])
                    ?>
                </div>
            </div>

            <hr>

            <?= $form->field($issueForm, 'tags')
                ->dropDownList(
                    \yii\helpers\ArrayHelper::map(
                        \tracker\models\Tag::find()
                            ->byUser(Yii::$app->user->id)
                            ->orderBy([\tracker\models\Tag::tableName() . '.name' => SORT_ASC])
                            ->all(),
                        'id', 'name'
                    ),
                    ['text' => 'Please select', 'multiple' => true]
                ); ?>

        </div>
    </div>

<?php endif ?>

<div class="row">
    <div class="col-md-12">
        <?php if ($isSpace) : ?>
            <?= $form->field($issueForm, 'notifyAssignors')->checkbox() ?>
        <?php endif; ?>

        <button type="submit" class="btn btn-block btn-primary"
            <?php if ($submitAjax) : ?>
                data-ui-loader
                data-action-click="editSubmit"
                data-action-url="<?= $this->context->contentContainer->createUrl('/' .
                    \tracker\Module::getIdentifier() .
                    '/issue/edit',
                    ['id' => $issueForm->id]) ?>"
            <?php endif; ?>
        >
            <?= Yii::t('TrackerIssuesModule.views', 'Save') ?>
        </button>
    </div>
</div>

<?php ActiveForm::end(); ?>

