<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \tracker\models\IssueSearch $searchModel
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 * @var boolean $canCreateNewIssue
 */

use tracker\models\Issue;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\StatusIssueWidget;
use yii\helpers\Html;
use yii\widgets\Pjax;

\tracker\assets\IssueAsset::register($this);
?>

<div class="panel panel-default">
    <div class="panel-body">

        <?php Pjax::begin() ?>

        <?= \humhub\widgets\GridView::widget([
            'options' => ['class' => 'table-responsive'],
            'tableOptions' => ['class' => 'table table-condensed'],
            'emptyText' => Yii::t('TrackerIssuesModule.views', 'No open issues...'),
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
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
                    'attribute' => 'started_at',
                    'format' => ['datetime', 'd MMM y, HH:mm'],
                ],
                [
                    'attribute' => 'deadline',
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return DeadlineIssueWidget::widget(['deadline' => $issue->deadline, 'short' => true]);
                    },
                ],
                [
                    'attribute' => 'finished_at',
                    'format' => ['datetime', 'd MMM y, HH:mm'],
                ],
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'value' => function (Issue $issue) {
                        return StatusIssueWidget::widget(['status' => $issue->status]);
                    },
                    'filter' => \tracker\enum\IssueStatusEnum::getList(),
                ],
                [
                    'label' => Yii::t('TrackerIssuesModule.views', 'Visibility'),
                    'format' => 'html',
                    'value' => function (Issue $issue) use ($contentContainer) {
                        $html = \tracker\widgets\VisibilityIssueWidget::widget(['visibilityContent' => $issue->content->visibility]);
                        if ($contentContainer instanceof \humhub\modules\space\models\Space) {
                            return $html;
                        }
                        if ($issue->content->getContainer() instanceof \humhub\modules\space\models\Space) {
                            $html .= ' ';
                            $html .= \humhub\modules\space\widgets\Image::widget([
                                'space' => $issue->content->getContainer(),
                                'link' => true,
                                'width' => 24,
                            ]);
                        } elseif ($issue->content->getContainer() instanceof \humhub\modules\user\models\User) {
                            $html .= ' ';
                            $html .= \humhub\modules\user\widgets\Image::widget([
                                'user' => $issue->content->getContainer(),
                                'link' => true,
                                'width' => 24,
                            ]);
                        }
                        return $html;
                    },
                ],
                [
                    'label' => Yii::t('TrackerIssuesModule.views', 'Owner'),
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

        <?php Pjax::end() ?>

    </div>
</div>


