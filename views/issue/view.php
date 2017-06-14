<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var Issue $issue
 */

use tracker\models\Issue;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\StatusIssueWidget;
use yii\helpers\Html;

tracker\assets\IssueAsset::register($this);
$formatter = Yii::$app->formatter;
?>

<div class="issue" id="issue_<?php echo $issue->id; ?>">

    <div class="row">
        <div class="col-md-8">

            <h4><?= Html::encode($issue->title) ?></h4>

            <p data-ui-markdown data-ui-show-more>
                <?= humhub\widgets\RichText::widget(['text' => $issue->description, 'record' => $issue]) ?>
            </p>

        </div>

        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-body">
                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Visibility') ?>">
                        <span class="label label-default"><i class="fa fa-dot-circle-o fa-fw"
                                                             aria-hidden="true"></i></span>
                        <?= \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]); ?>
                    </div>

                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Status') ?>">
                        <span class="label label-default"><i class="fa fa-flag fa-fw" aria-hidden="true"></i></span>
                        <?= StatusIssueWidget::widget(['status' => $issue->status]) ?>
                    </div>


                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Priority') ?>">
                        <span class="label label-default"><i class="fa fa-exclamation fa-fw"
                                                             aria-hidden="true"></i></span>
                        <?= \tracker\widgets\PriorityIssueWidget::widget(['priority' => $issue->priority]) ?>
                    </div>

                    <hr>

                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                        'From this time recommended begin to start work') ?>">
                        <span class="label label-default"><i class="fa fa-calendar-o fa-fw"
                                                             aria-hidden="true"></i></span>
                        <span class="label label-default">
                            <?= $formatter->asDatetime($issue->started_at, 'HH:mm, eee d MMMM y ') ?>
                        </span>
                    </div>

                    <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                        'Deadline') ?>">
                        <span class="label label-default"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i></span>
                        <?= DeadlineIssueWidget::widget(['deadline' => $issue->deadline, 'short' => true]) ?>
                    </div>

                    <?php if ($issue->finished_at) : ?>
                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views',
                            'Finished at') ?>">
                            <span class="label label-default"><i class="fa fa-calendar-check-o fa-fw"
                                                                 aria-hidden="true"></i></span>
                            <span class="label label-default">
                             <?= $formatter->asDatetime($issue->finished_at, 'HH:mm, eee d MMMM y ') ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (count($issue->personalTags) > 0) : ?>
                        <hr>
                        <div data-toggle="tooltip" title="<?= Yii::t('TrackerIssuesModule.views', 'Tags') ?>">
                            <span class="label label-default"><i class="fa fa-tags fa-fw" aria-hidden="true"></i></span>
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

    <?php
    /** @var Issue $parent */
    $parent = $issue->getParent()->readable()->one();
    $parentNotReadable = $issue->getParent()->one();
    ?>
    <?php if ($parent !== null)  : ?>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <strong><?= Yii::t('TrackerIssuesModule.views', 'This issue is subtask for the issue') ?></strong>
                <h1 class="panel-title">
                    <?= Html::a(Html::encode($parent->title), $parent->content->getUrl()) ?>
                </h1>
            </div>
            <div class="panel-body">
                <p data-ui-markdown data-ui-show-more>
                    <?= humhub\widgets\RichText::widget([
                        'text' => $parent->description,
                        'record' => $parent,
                    ]) ?>
                </p>

            </div>
        </div>
    <?php elseif($parentNotReadable !== null) : ?>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <strong><?= Yii::t('TrackerIssuesModule.views', 'This issue is subtask for the issue') ?></strong>
                <h1 class="panel-title"><?= Html::encode($parentNotReadable->title) ?></h1>
            </div>
            <div class="panel-body">
                <p data-ui-markdown data-ui-show-more>
                    <?= humhub\widgets\RichText::widget([
                        'text' => $parentNotReadable->description,
                        'record' => $parentNotReadable,
                    ]) ?>
                </p>

            </div>
        </div>
    <?php endif ?>

    <?php if (count($issue->assignees) > 0) : ?>
        <div class="table-responsive">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th></th>
                    <th>
                        <i class="fa fa-users" aria-hidden="true"></i>
                        <?= Yii::t('TrackerIssuesModule.views', 'Assignee') ?>
                    </th>
                    <th>
                        <i class="fa fa-calendar-o" aria-hidden="true"></i>
                        <?= Yii::t('TrackerIssuesModule.views', 'Assigned at') ?>
                    </th>
                    <th>
                        <i class="fa fa-calendar-plus-o" aria-hidden="true"></i>
                        <?= Yii::t('TrackerIssuesModule.views', 'Adopted at') ?>
                    </th>
                    <th>
                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        <?= Yii::t('TrackerIssuesModule.views', 'Finished at') ?>
                    </th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($issue->assignees as $assigner) : ?>
                    <tr>
                        <td>
                            <img src="<?= $assigner->user->getProfileImage()->getUrl(); ?>"
                                 class="img-rounded tt img_margin"
                                 height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                 style="width: 24px; height: 24px;">
                        </td>
                        <td>
                            <a href="<?= $assigner->user->getUrl(); ?>">
                                <small><?= Html::encode($assigner->user->displayName); ?></small>
                            </a>
                        </td>
                        <td>
                            <?= $formatter->asDatetime($assigner->created_at, 'HH:mm, eee. d MMM yyyy') ?>
                        </td>
                        <td>
                            <?php if ($assigner->view_mark) : ?>
                                <?= $formatter->asDatetime($assigner->viewed_at, 'HH:mm, eee. d MMM yyyy') ?>
                            <?php else: ?>
                                <?php if ($assigner->user_id == Yii::$app->user->id) : ?>
                                    <?= \humhub\widgets\AjaxButton::widget([
                                        'label' => Yii::t('TrackerIssuesModule.views', 'Adopted'),
                                        'ajaxOptions' => [
                                            'type' => 'POST',
                                            'success' => new yii\web\JsExpression('function(){humhub.modules.client.reload();}'),
                                            'url' => $issue->content->container->createUrl(
                                                '/' . \tracker\Module::getIdentifier() . '/issue/mark-adopted',
                                                ['id' => $assigner->id,]
                                            ),
                                        ],
                                        'htmlOptions' => [
                                            'class' => 'btn btn-primary btn-xs btn-block',
                                        ],
                                    ]);
                                    ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($assigner->finish_mark) : ?>
                                <?= $formatter->asDatetime($assigner->finished_at, 'HH:mm, eee. d MMM yyyy') ?>
                            <?php else: ?>
                                <?php if ($assigner->user_id == Yii::$app->user->id) : ?>
                                    <?php if ($assigner->view_mark && !$assigner->finish_mark): ?>
                                        <?= \humhub\widgets\AjaxButton::widget([
                                            'label' => Yii::t('TrackerIssuesModule.views', 'Done'),
                                            'ajaxOptions' => [
                                                'type' => 'POST',
                                                'success' => new yii\web\JsExpression('function(){humhub.modules.client.reload();}'),
                                                'url' => $issue->content->container->createUrl(
                                                    '/' . \tracker\Module::getIdentifier() . '/issue/mark-done',
                                                    ['id' => $assigner->id,]
                                                ),
                                            ],
                                            'htmlOptions' => [
                                                'class' => 'btn btn-primary btn-xs btn-block',
                                            ],
                                        ]);
                                        ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <?php
    $dataProvider = new \yii\data\ActiveDataProvider(['query' => $issue->getSubtasks()->readable()]);
    if ($dataProvider->getTotalCount() > 0) : ?>
        <div class="panel panel-warning">
            <div class="panel-heading">
                <h1 class="panel-title">
                    <?= Yii::t('TrackerIssuesModule.views', 'This issue has subtasks') ?>:
                </h1>
            </div>
            <div class="panel-body">

                <?= $this->render('__gridView', [
                    'contentContainer' => $issue->content->getContainer(),
                    'dataProvider' => $dataProvider,
                    'searchModel' => null,
                ]) ?>

            </div>
        </div>
    <?php endif ?>

</div>
