<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var Issue $issue
 */

use humhub\libs\Html;
use tracker\models\Issue;

tracker\assets\IssueAsset::register($this);
$formatter = Yii::$app->formatter;
?>
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
                <?php if (!$issue->continuous_use) : ?>
                    <th>
                        <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                        <?= Yii::t('TrackerIssuesModule.views', 'Finished at') ?>
                    </th>
                <?php endif; ?>
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
                            <span data-toggle="tooltip"
                                  title="<?= $formatter->asDatetime($assigner->created_at, 'HH:mm, d MMMM yyyy') ?>">
                                <?= $formatter->asDatetime($assigner->created_at, 'HH:mm, eee. d MMM') ?>
                            </span>
                    </td>
                    <td>
                        <?php if ($assigner->view_mark) : ?>
                            <span data-toggle="tooltip"
                                  title="<?= $formatter->asDatetime($assigner->viewed_at, 'HH:mm, d MMMM yyyy') ?>">
                                <?= $formatter->asDatetime($assigner->viewed_at, 'HH:mm, eee. d MMM') ?>
                                </span>
                        <?php elseif ($assigner->user_id == Yii::$app->user->id) : ?>

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

                        <?php elseif ($issue->continuous_use && $issue->content->canEdit()) : ?>
                            <?= Html::button(
                                Yii::t('TrackerIssuesModule.views', 'Remind'),
                                [
                                    'class' => 'btn btn-xs btn-default',
                                    'data-action-click' => 'tracker.remindIssue',
                                    'data-action-url' => $issue->content->container->createUrl(
                                        '/' . \tracker\Module::getIdentifier() . '/issue/remind',
                                        ['id' => $issue->id, 'user' => $assigner->user->getAuthKey(),]
                                    )
                                ]
                            ) ?>
                        <?php endif; ?>
                    </td>
                    <?php if (!$issue->continuous_use) : ?>
                        <td>
                            <?php if ($assigner->finish_mark) : ?>
                                <span data-toggle="tooltip"
                                      title="<?= $formatter->asDatetime($assigner->finished_at, 'HH:mm, d MMMM yyyy') ?>">
                                <?= $formatter->asDatetime($assigner->finished_at, 'HH:mm, eee. d MMM') ?>
                                </span>
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
                                <?php else: ?>
                                    <?php if ($issue->content->canEdit()) : ?>
                                        <?= Html::button(
                                            Yii::t('TrackerIssuesModule.views', 'Remind'),
                                            [
                                                'class' => 'btn btn-xs btn-default',
                                                'data-action-click' => 'tracker.remindIssue',
                                                'data-action-url' => $issue->content->container->createUrl(
                                                    '/' . \tracker\Module::getIdentifier() . '/issue/remind',
                                                    ['id' => $issue->id, 'user' => $assigner->user->getAuthKey(),]
                                                )
                                            ]
                                        ) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                <?php endif; ?>

                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
