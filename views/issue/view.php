<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var Issue $issue
 */

use humhub\libs\Html;
use tracker\enum\IssuePriorityEnum;
use tracker\enum\IssueStatusEnum;
use tracker\models\Issue;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\StatusIssueWidget;

tracker\assets\IssueAsset::register($this);
$formatter = Yii::$app->formatter;
?>

<div class="issue" id="issue_<?= $issue->id; ?>">

    <div class="row">
        <div class="col-md-8">
            <div class="issue-description">
                <?php if ($issue->priority > IssuePriorityEnum::TYPE_NORMAL) : ?>
                    <?= \tracker\widgets\PriorityIssueWidget::widget(['priority' => $issue->priority, 'extraCssClass' => 'issue-label_full-width']) ?>
                <?php endif; ?>
                <p data-ui-markdown data-ui-show-more>
                    <?= \humhub\modules\content\widgets\richtext\RichText::output($issue->description, ['record' => $issue,]) ?>
                </p>
            </div>

            <?= $this->render('__issue_assignees', ['issue' => $issue]) ?>

        </div>

        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-body">

                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Status') ?>">
                        <span class="label label-default"><i class="fa fa-flag fa-fw" aria-hidden="true"></i></span>
                        <?= StatusIssueWidget::widget(['status' => $issue->status]) ?>
                    </div>

                    <div class="collapse <?= $issue->status == IssueStatusEnum::TYPE_FINISHED ? '' : 'in' ?>"
                         id="issue-details-<?= $issue->getUniqueId(); ?>">

                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Priority') ?>">
                        <span class="label label-default"><i class="fa fa-exclamation fa-fw"
                                                             aria-hidden="true"></i></span>
                            <?= \tracker\widgets\PriorityIssueWidget::widget(['priority' => $issue->priority]) ?>
                        </div>

                        <hr>

                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Visibility') ?>">
                        <span class="label label-default">
                          <i class="fa fa-dot-circle-o fa-fw" aria-hidden="true"></i>
                        </span>
                            <?= \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]); ?>
                            <br>
                            <p style="margin-left: 26px;display: block;">
                                <small><?= Html::containerLink($issue->content->container); ?></small>
                            </p>
                        </div>

                        <hr>

                        <?= \tracker\widgets\ContinuousUseWidget::widget(['show' => $issue->continuous_use]) ?>

                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                            'From this time recommended begin to start work') ?>">
                        <span class="label label-default"><i class="fa fa-calendar-o fa-fw"
                                                             aria-hidden="true"></i></span>
                            <span class="label label-default">
                            <?= $formatter->asDatetime($issue->started_at, 'eee d MMMM y, HH:mm') ?>
                        </span>
                        </div>

                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                            'Deadline') ?>">
                            <span class="label label-default"><i class="fa fa-clock-o fa-fw"
                                                                 aria-hidden="true"></i></span>
                            <?= DeadlineIssueWidget::widget(['deadline' => $issue->deadline, 'short' => true]) ?>
                        </div>

                        <?php if ($issue->finished_at) : ?>
                            <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                                'Finished at') ?>">
                            <span class="label label-default"><i class="fa fa-calendar-check-o fa-fw"
                                                                 aria-hidden="true"></i></span>
                                <span class="label label-default">
                             <?= $formatter->asDatetime($issue->finished_at, 'eee d MMMM y, HH:mm') ?>
                            </span>
                            </div>
                        <?php endif; ?>

                        <?php if (count($issue->personalTags) > 0) : ?>
                            <hr>
                            <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Tags') ?>">
                                <span class="label label-default"><i class="fa fa-tags fa-fw"
                                                                     aria-hidden="true"></i></span>
                                <?= \tracker\widgets\TagsWidget::widget([
                                    'tagsModels' => $issue->personalTags,
                                    'asLink' => true,
                                ]) ?>
                            </div>

                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
