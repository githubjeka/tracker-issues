<?php

use humhub\modules\stream\assets\StreamAsset;
use yii\helpers\Html;

/* @var $this \humhub\components\View */
/* @var $filterNav string */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

StreamAsset::register($this);
?>

<?php if ($contentContainer && $contentContainer->isArchived()) : ?>
    <span class="label label-warning pull-right" style="margin-top:10px;">
        <?= Yii::t('ContentModule.widgets_views_label', 'Archived'); ?>
    </span>
<?php endif; ?>

<?= $filterNav ?>

<?= Html::beginTag('div', $options) ?>

<div class="s2_stream">
    <div class="s2_streamContent" data-stream-content></div>
</div>

<?= Html::endTag('div') ?>