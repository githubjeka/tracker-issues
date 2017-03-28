<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProviders
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 * @var boolean $canCreateNewIssue
 */

use tracker\models\Issue;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\StatusIssueWidget;
use yii\helpers\Html;

\tracker\assets\IssueAsset::register($this);
?>

<div class="panel panel-default">
    <div class="panel-body">

        <?php if ($canCreateNewIssue): ?>
            <a href="<?php echo $contentContainer->createUrl('create'); ?>" class="btn btn-primary"
               data-target="#globalModal">
                <i class="fa fa-plus"></i> <?php echo Yii::t('TrackerIssuesModule.views', 'New issue'); ?>
            </a>
        <?php endif; ?>

        <?= \humhub\widgets\GridView::widget([
            'options' => ['class' => 'table-responsive'],
            'tableOptions' => ['class' => 'table table-condensed'],
            'emptyText' => Yii::t('TrackerIssuesModule.views', 'No open issues...'),
            'dataProvider' => $dataProviders,
            'columns' => [
                [
                    'label' => '#',
                    'format' => 'html',
                    'value' => function (Issue $issue, $key, $index, $column) {
                        $pagination = $column->grid->dataProvider->getPagination();
                        if ($pagination !== false) {
                            return Html::a($pagination->getOffset() + $index + 1, $issue->content->getUrl());
                        } else {
                            return Html::a($index + 1, $issue->content->getUrl());
                        }
                    },
                ],
                [
                    'attribute' => 'title',
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return Html::a(Html::encode($issue->title), $issue->content->getUrl());
                    },
                ],
                [
                    'label' => Yii::t('TrackerIssuesModule.views', 'Visibility'),
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]);
                    },
                ],
                [
                    'attribute' => 'deadline',
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return DeadlineIssueWidget::widget(['deadline' => $issue->deadline, 'short' => true]);
                    },
                ],
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return StatusIssueWidget::widget(['status' => $issue->status]);
                    },
                ],
                [
                    'label' => Yii::t('TrackerIssuesModule.views', 'Creator'),
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return '<a href="' . $issue->content->user->getUrl() . '">
  <img src="' . $issue->content->user->getProfileImage()->getUrl() . '" class="img-rounded tt img_margin"
       height="24" width="24" alt="24x24" data-src="holder.js/24x24"
       style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top" title=""
       data-original-title="' . Html::encode($issue->content->user->displayName) . '">
</a>';
                    },
                ],
            ],
        ]) ?>
    </div>
</div>



