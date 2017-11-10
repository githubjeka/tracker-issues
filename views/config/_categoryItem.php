<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\widgets\ModalButton;

/* @var $model \tracker\models\DocumentCategory */
?>
<div class="media" style="margin-top:5px">
    <div class="media-body">
        <div class="clearfix">
            <div class="pull-right">
                <?= ModalButton::danger()
                    ->post(['/' . \tracker\Module::getIdentifier() . '/config/delete-category', 'id' => $model->id])
                    ->confirm(
                        null,
                        Yii::t('TrackerIssuesModule.views', 'Are you sure you want to delete this item?'),
                        Yii::t('TrackerIssuesModule.views', 'Delete'))
                    ->icon('fa-times')
                    ->xs() ?>
            </div>

            <?= ModalButton::primary()
                ->load(['/' . \tracker\Module::getIdentifier() . '/config/edit-category', 'id' => $model->id])
                ->icon('fa-pencil')
                ->xs() ?>

            <?= \tracker\widgets\DocumentCategoryLabel::widget(['category' => $model]) ?>
        </div>
    </div>
</div>
