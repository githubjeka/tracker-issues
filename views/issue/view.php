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
$assigners = $issue->getAssignees()->all();
?>

<p>
    <span class="label label-info"><?= $issue->getContentName(); ?></span>
    <?= StatusIssueWidget::widget(['status' => $issue->status]) ?>
    <?= DeadlineIssueWidget::widget(['deadline' => $issue->deadline]) ?>
    <?= \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]); ?>
</p>

<h4><?= Html::encode($issue->title) ?></h4>

<div class="issue" id="issue_<?php echo $issue->id; ?>">

    <p data-ui-markdown data-ui-show-more style="overflow: hidden;">
        <?= humhub\widgets\RichText::widget(['text' => $issue->description, 'record' => $issue]) ?>
    </p>

    <?php if (count($assigners) > 0) : ?>
        <hr>

        <div class="table-responsive">
            <table class="table table-condensed">
                <thead>
                <tr>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Assignee') ?></th>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Adopted in work') ?></th>
                    <th><?= Yii::t('TrackerIssuesModule.views', 'Performed') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>
                        <?php foreach (
                            $issue->getAssignees()->andWhere('view_mark = 0 AND finish_mark = 0')->all() as $assigner
                        ) : ?>
                            <a href="<?php echo $assigner->user->getUrl(); ?>">
                                <img src="<?php echo $assigner->user->getProfileImage()->getUrl(); ?>"
                                     class="img-rounded tt img_margin"
                                     height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                     style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                     title=""
                                     data-original-title="<?php echo Html::encode($assigner->user->displayName); ?>">
                            </a>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach (
                            $issue->getAssignees()->andWhere('view_mark = 1 AND finish_mark = 0')->all() as $assigner
                        ) : ?>
                            <a href="<?php echo $assigner->user->getUrl(); ?>">
                                <img src="<?php echo $assigner->user->getProfileImage()->getUrl(); ?>"
                                     class="img-rounded tt img_margin"
                                     height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                     style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                     title=""
                                     data-original-title="<?php echo Html::encode($assigner->user->displayName); ?>">
                            </a>
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php foreach (
                            $issue->getAssignees()->andWhere('finish_mark = 1')->all() as $assigner
                        ) : ?>
                            <a href="<?php echo $assigner->user->getUrl(); ?>">
                                <img src="<?php echo $assigner->user->getProfileImage()->getUrl(); ?>"
                                     class="img-rounded tt img_margin"
                                     height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                     style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top"
                                     title=""
                                     data-original-title="<?php echo Html::encode($assigner->user->displayName); ?>">
                            </a>
                        <?php endforeach; ?>
                    </td>
                </tr>
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
            <?php endif; ?>
        <?php endforeach; ?>

    <?php endif; ?>
</div>
