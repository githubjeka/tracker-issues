<a data-target="#globalModal"
   href="<?= \yii\helpers\Url::to(['/' . \tracker\Module::getIdentifier() . '/tag/designate','id'=>$object->id]); ?>">
    <?= \Yii::t('TrackerIssuesModule.views', 'Designate tag') ?>
</a>
