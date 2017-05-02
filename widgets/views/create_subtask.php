<a data-target="#globalModal"
   href="<?= $object->content->container->createUrl(
       '/' . \tracker\Module::getIdentifier() . '/issue/create-subtask',
       ['id' => $object->id]
   ); ?>">
    <?= \Yii::t('TrackerIssuesModule.views', 'Add Subtask') ?>
</a>
