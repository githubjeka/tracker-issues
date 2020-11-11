<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

use humhub\libs\Html;
use humhub\modules\content\widgets\richtext\RichText;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\PriorityIssueWidget;
use tracker\widgets\StatusIssueWidget;
use tracker\widgets\TagsWidget;

/**
 * @var $this \humhub\components\View
 * @var \tracker\models\Issue $model
 */

$issue = $model;
$formatter = Yii::$app->formatter;
?>
<div class="panel issue-panel">
    <div class="panel-heading">
        <div class="pull-right">
            <span class="label label-default">
                <?= $formatter->asDatetime($issue->started_at, 'short') ?>
            </span>
            <?= StatusIssueWidget::widget(['status' => $issue->status]) ?>
        </div>
        <h5 class="panel-title">
            <?= Html::a(
                Html::tag('strong', $issue->title ? Html::encode($issue->title) : "#$issue->id"),
                $issue->content->getUrl()
            ) ?>
        </h5>
    </div>
    <div class="panel-body">
        <div>
            <?php foreach ($issue->documents as $document) : ?>
                <?php
                echo Html::a(
                    Html::tag(
                        'i',
                        '&nbsp;' . Html::encode($document->number),
                        ['class' => 'fa fa-files-o']),
                    ['document/view', 'id' => $document->id],
                    ['class' => 'mark']
                ) ?>
            <?php endforeach; ?>
            <?= RichText::widget(['maxLength' => 150, 'minimal' => true, 'text' => $issue->description,]) ?>
        </div>
        <div>
            <hr>
            <?= \humhub\modules\user\widgets\Image::widget([
                'user' => $issue->content->createdBy,
                'link' => true,
                'width' => 20,
                'showTooltip' => true,
                'tooltipText' => Html::encode($issue->content->createdBy->getDisplayName())
            ]) ?>
            |
            <?php
            foreach ($issue->assignees as $assigner) {
                echo \humhub\modules\user\widgets\Image::widget([
                        'user' => $assigner->user,
                        'link' => true,
                        'width' => 16,
                        'showTooltip' => true,
                        'tooltipText' => Html::encode($assigner->user->getDisplayName())
                    ]) . ' ';
            } ?>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="col-md-8">
                <div>
                    <?= \tracker\widgets\ContinuousUseWidget::widget(['show' => $issue->continuous_use]) ?>
                    <?= DeadlineIssueWidget::widget([
                        'deadline' => $issue->deadline,
                        'short' => true,
                    ]) ?> |
                    <?= PriorityIssueWidget::widget(['priority' => $issue->priority]) ?> |
                    <?= TagsWidget::widget(['tagsModels' => $issue->personalTags, 'asLink' => true,]) ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-right">
                    <?php
                    $html = \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]);
                    if ($issue->content->getContainer() instanceof \humhub\modules\space\models\Space) {
                        $html .= ' ';
                        $html .= '<small class="text-uppercase">' . Html::encode($issue->content->getContainer()->getDisplayName()) . '</small>';
                    } elseif ($issue->content->getContainer() instanceof \humhub\modules\user\models\User) {
                        $html .= ' ';
                        $html .= \humhub\modules\user\widgets\Image::widget([
                            'user' => $issue->content->getContainer(),
                            'link' => false,
                            'width' => 16,
                            'showTooltip' => true,
                            'tooltipText' => Html::encode($issue->content->getContainer()->getDisplayName())
                        ]);
                    }
                    echo $html;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
