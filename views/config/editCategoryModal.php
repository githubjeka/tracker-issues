<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\widgets\ActiveForm;
use humhub\widgets\ColorPickerField;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/**
 * @var $this \humhub\components\View
 * @var $model \tracker\models\DocumentCategory
 */

$title = ($model->isNewRecord)
    ? Yii::t('TrackerIssuesModule.views', 'Create new category')
    : Yii::t('TrackerIssuesModule.views', 'Edit the category');
?>

<?php ModalDialog::begin(['header' => $title]); ?>
<?php $form = ActiveForm::begin() ?>
<div class="modal-body">
    <div id="document-category-text-color" class="form-group space-color-chooser-edit" style="margin-top: 5px;">
        <?= ColorPickerField::widget(['model' => $model, 'container' => 'document-category-text-color']); ?>
        <?= $form
            ->field(
                $model,
                'name',
                ['template' => '{label}<div class="input-group"><span class="input-group-addon"><i></i></span>{input}</div>{error}{hint}']
            )
            ->textInput([
                'placeholder' => Yii::t('TrackerIssuesModule.views', 'The name of document category'),
                'maxlength' => true
            ])
            ->label(false) ?>
    </div>
    <p class="hint-block">
        <?= Yii::t('TrackerIssuesModule.views', 'The color will be applied to text of category') ?>
    </p>
</div>
<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>
<?php ActiveForm::end() ?>
<?php ModalDialog::end() ?>
