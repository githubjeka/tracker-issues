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

<h4><?= Html::encode($issue->title) ?></h4>

<div class="issue" id="issue_<?php echo $issue->id; ?>">

    <p data-ui-markdown data-ui-show-more>
        <?= humhub\widgets\RichText::widget(['text' => $issue->description, 'record' => $issue]) ?>
    </p>

    <div class="panel">
        <div class="panel-body">
            <div class="row-fluid bg-info">

                <div class="col-xs-4">
                    <?= Yii::t('TrackerIssuesModule.views', 'Started at') ?><br>
                    <?= $formatter->asDatetime($issue->started_at, 'HH:mm, eee d MMMM y ') ?>
                </div>

                <div class="col-xs-4">
                    <?= DeadlineIssueWidget::widget(['deadline' => $issue->deadline]) ?><br>
                    <?= StatusIssueWidget::widget(['status' => $issue->status]) ?>
                </div>

                <div class="col-xs-4">
                    <?php if ($issue->finished_at) : ?>
                        <?= Yii::t('TrackerIssuesModule.views', 'Finished at') ?><br>
                        <?= $formatter->asDatetime($issue->finished_at, 'HH:mm, eee d MMMM y ') ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($issue->assignees) > 0) : ?>
        <div class="table-responsive">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Assignee') ?></th>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Assigned at') ?></th>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Adopted at') ?></th>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Finished at') ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($issue->assignees as $assigner) : ?>
                    <tr>
                        <td>
                            <a href="<?= $assigner->user->getUrl(); ?>">
                                <img src="<?= $assigner->user->getProfileImage()->getUrl(); ?>"
                                     class="img-rounded tt img_margin"
                                     height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                     style="width: 24px; height: 24px;">
                                <br>
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
                                <strong>-</strong>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($assigner->finish_mark) : ?>
                                <?= $formatter->asDatetime($assigner->finished_at, 'HH:mm, eee. d MMM yyyy') ?>
                            <?php else: ?>
                                <strong>-</strong>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php foreach ($issue->getAssignees()->all() as $assigner) : ?>
            <?php if ($assigner->user_id == Yii::$app->user->id) : ?>
                <?php if (!$assigner->view_mark) : ?>
                    <?= \humhub\widgets\AjaxButton::widget([
                        'label' => Yii::t('TrackerIssuesModule.views', 'Adopted'),
                        'ajaxOptions' => [
                            'type' => 'POST',
                            'success' => new yii\web\JsExpression(
                                'function(html){ $(\'div[data-content-key="' . $issue->content->id .
                                '"]\').html(html); }'),
                            'url' => $issue->content->container->createUrl(
                                '/' . \tracker\Module::getIdentifier() . '/issue/mark-adopted',
                                ['id' => $assigner->id,]
                            ),
                        ],
                        'htmlOptions' => [
                            'class' => 'btn btn-primary btn-block',
                        ],
                    ]);
                    ?>
                <?php elseif ((!$assigner->finish_mark)): ?>
                    <?= \humhub\widgets\AjaxButton::widget([
                        'label' => Yii::t('TrackerIssuesModule.views', 'Done'),
                        'ajaxOptions' => [
                            'type' => 'POST',
                            'success' => new yii\web\JsExpression(
                                'function(html){ $(\'div[data-content-key="' . $issue->content->id .
                                '"]\').html(html); }'),
                            'url' => $issue->content->container->createUrl(
                                '/' . \tracker\Module::getIdentifier() . '/issue/mark-done',
                                ['id' => $assigner->id,]
                            ),
                        ],
                        'htmlOptions' => [
                            'class' => 'btn btn-primary btn-block',
                        ],
                    ]);
                    ?>
                <?php endif; ?>
                <?php break; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <br>
    <?php endif; ?>

    <strong><?= Yii::t('TrackerIssuesModule.views', 'Tags') ?>:</strong>
    <?= \tracker\widgets\TagsWidget::widget(['tagsModels' => $issue->personalTags, 'asLink' => true]) ?>

    <hr>

    <?php
    /** @var Issue $parent */
    $parent = $issue->getParent()->readable()->one();
    ?>
    <?php if ($parent !== null)  : ?>
        <div class="panel">
            <div class="panel-body">
                <?= Yii::t('TrackerIssuesModule.views', 'This issue is subtask for the issue') ?>

                <h5>
                    <?= Html::a(Html::encode($parent->title), $issue->content->getUrl()) ?>
                </h5>

                <p data-ui-markdown data-ui-show-more>
                    <?= humhub\widgets\RichText::widget([
                        'text' => $issue->description,
                        'record' => $issue,
                    ]) ?>
                </p>

            </div>
        </div>
    <?php endif ?>

    <?php
    $dataProvider = new \yii\data\ActiveDataProvider(['query' => $issue->getSubtasks()->readable()]);
    if ($dataProvider->getTotalCount() > 0) : ?>
        <div class="panel">
            <div class="panel-body">
                <?= Yii::t('TrackerIssuesModule.views', 'This issue has subtasks') ?>

                <?= $this->render('__gridView', [
                    'contentContainer' => $issue->content->getContainer(),
                    'dataProvider' => $dataProvider,
                    'searchModel' => null,
                ]) ?>

            </div>
        </div>
    <?php endif ?>

</div>
