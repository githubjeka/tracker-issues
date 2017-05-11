<?php if ($object->content->canEdit()) : ?>

    <?= \humhub\libs\Html::a(\Yii::t('TrackerIssuesModule.views', 'Finish issue'), '#',
        [
            'data-action-click' => 'tracker.finishIssue',
            'data-action-url' => $object->content->container->createUrl(
                '/' . \tracker\Module::getIdentifier() . '/issue/finish-issue',
                ['id' => $object->id]
            ),
        ]
    ) ?>

<?php endif; ?>
