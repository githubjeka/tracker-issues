<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 * @var string $deadline
 * @var boolean $short
 * @var string $startedTime
 */
?>

<?php if (empty($deadline))  : ?>
    <span class="label label-default">
         <?= \Yii::t('TrackerIssuesModule.views', 'Has not deadline') ?>
    </span>
<?php else: ?>
    <?php
    $formatter = \Yii::$app->formatter;
    $color = 'label-info';
    $dateDeadline = \DateTime::createFromFormat('Y-m-d', $formatter->asDatetime($deadline, 'php:Y-m-d'));
    $dateNow = (new \DateTime())->add(new DateInterval('P1D'));
    if ($dateNow > $dateDeadline) {
        $color = 'label-danger';
    }
    ?>

    <?php if (isset($startTime))  : ?>
        <span class="label label-default">
            <?= $formatter->asDatetime($startTime, 'd MMM y, HH:mm') ?>
        </span>&nbsp;-&nbsp;
    <?php endif; ?>

    <span class="label <?= $color ?>">
        <?php if (!$short)  : ?>
            <?= \Yii::t('TrackerIssuesModule.views', 'Must be completed by') ?>&nbsp;
        <?php endif; ?>

        <?= $formatter->asDatetime($deadline, 'd MMM y, HH:mm') ?>
    </span>
<?php endif; ?>
