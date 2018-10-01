<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\widgets\ActiveForm;

/**
 * @var $this \humhub\components\View
 * @var \tracker\models\IssueSearch $searchModel
 * @var ActiveForm $form
 */
$formatter = Yii::$app->formatter;
?>
<?php $form = ActiveForm::begin(['method' => 'get']) ?>

<button class="btn btn-link btn-block text-uppercase">
    <?= Yii::t('TrackerIssuesModule.views', 'Search') ?>
</button>

<?php if (!$searchModel->isConstantly) : ?>
    <?= $form->field($searchModel, 'onlyNotFulfilled')->radioList(
        [
            '0' => Yii::t('TrackerIssuesModule.views', 'All'),
            '1' => Yii::t('TrackerIssuesModule.views', 'Not fulfilled'),
            '2' => Yii::t('TrackerIssuesModule.views', 'For me'),
        ]
    )->label(false); ?>
<?php endif; ?>

<?=
$form->field($searchModel, 'assignee')
    ->widget(\humhub\modules\user\widgets\UserPickerField::class, ['placeholder' => '  ',])
    ->label(Yii::t('TrackerIssuesModule.views', 'Assignee'))
?>

<?= $form->field($searchModel, 'space')
    ->widget(humhub\modules\space\widgets\SpacePickerField::class)
    ->label(Yii::t('SpaceModule.base', 'Space')) ?>

<?= $form->field($searchModel, 'status')
    ->dropDownList(\tracker\enum\IssueStatusEnum::getList())
    ->label(Yii::t('TrackerIssuesModule.views', 'Status')) ?>

<?= $form->field($searchModel, 'title')
    ->label(Yii::t('TrackerIssuesModule.views', 'Name')) ?>

<?= $form->field($searchModel, 'document')
    ->label(Yii::t('TrackerIssuesModule.views', 'Document')) ?>

<?= \tracker\widgets\PeriodFilterWidget::widget(['searchModel' => $searchModel]) ?>

<hr>

<?= $form->field($searchModel, 'tag')
    ->checkboxList($searchModel->listTags())
    ->label(false) ?>

<button class="btn btn-success btn-block text-uppercase">
    <?= Yii::t('TrackerIssuesModule.views', 'Search') ?>
</button>

<?php ActiveForm::end() ?>

