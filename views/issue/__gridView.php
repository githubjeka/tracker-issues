<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \tracker\models\IssueSearch|null $searchModel
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 */

use tracker\models\Issue;
use tracker\widgets\DeadlineIssueWidget;
use tracker\widgets\StatusIssueWidget;
use yii\helpers\Html;

$formatter = Yii::$app->formatter;
?>

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
            'label' => Yii::t('TrackerIssuesModule.views', 'Tags'),
            'format' => 'raw',
            'value' => function (Issue $issue) {
                return \tracker\widgets\TagsWidget::widget([
                    'tagsModels' => $issue->personalTags,
                    'asLink' => true,
                ]);
            },
            'visible' => isset($hideTagsColumn) ? !$hideTagsColumn : true,
            'filter' => $searchModel ? \yii\bootstrap\Html::activeCheckboxList($searchModel, 'tag',
                $searchModel->listTags()) : false,
        ],
        [
            'attribute' => 'document',
            'label' => Yii::t('TrackerIssuesModule.views', 'Document'),
            'format' => 'html',
            'value' => function (Issue $issue) use ($formatter) {
                $html = '';
                foreach ($issue->documents as $document) {
                    $html .= Html::beginTag('strong');
                    $html .= Html::a(Html::encode($document->number), ['document/view', 'id' => $document->id]);
                    $html .= Html::endTag('strong');
                    $html .= Html::tag('br');
                    $html .= Html::beginTag('small');
                    $html .= $formatter->asDate($document->registered_at, 'short');
                    $html .= Html::endTag('small');
                }
                return $html;
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
            'attribute' => 'deadline',
            'label' => Yii::t('TrackerIssuesModule.views', 'Validity period'),
            'format' => 'html',
            'value' => function (Issue $issue) {
                return DeadlineIssueWidget::widget([
                    'startTime' => $issue->started_at,
                    'deadline' => $issue->deadline,
                    'short' => true,
                ]);
            },
            'filter' => \tracker\widgets\PeriodFilterWidget::widget(['searchModel' => $searchModel]),
        ],
        [
            'attribute' => 'priority',
            'format' => 'html',
            'value' => function (Issue $issue) {
                return \tracker\widgets\PriorityIssueWidget::widget(['priority' => $issue->priority]);
            },
        ],
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function (Issue $issue) {
                return StatusIssueWidget::widget(['status' => $issue->status]);
            },
            'filter' => \tracker\enum\IssueStatusEnum::getList(),
            'filterInputOptions' => ['class' => 'select-form table-filter', 'id' => null],
        ],
        [
            'label' => Yii::t('TrackerIssuesModule.views', 'Owner'),
            'format' => 'html',
            'value' => function (Issue $issue) {
                return '<a href="' . $issue->content->user->getUrl() . '">
  <img src="' . $issue->content->user->getProfileImage()->getUrl() . '" class="img-rounded tt img_margin"
       height="24" width="24" alt="24x24" data-src="holder.js/24x24"
       style="width: 24px; height: 24px;" data-toggle="tooltip" data-placement="top" title=""
       data-original-title="' . Html::encode($issue->content->user->getDisplayName()) . '">
</a>';
            },
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
            'label' => Yii::t('TrackerIssuesModule.views', 'Assignee'),
            'format' => 'html',
            'value' => function (Issue $issue) {
                $html = '';
                foreach ($issue->assignees as $assigner) {
                    $html .= '<a href="' . $assigner->user->getUrl() . '"><img src="' .
                             $assigner->user->getProfileImage()->getUrl() . '" class="img-rounded tt img_margin"
                                   height="24" width="24" alt="24x24" data-src="holder.js/24x24"
                                   style="width:24px;height:24px;" data-toggle="tooltip" data-placement="top" title=""
                                   data-original-title="oo' . Html::encode($assigner->user->getDisplayName()) .
                             '"></a>';
                }
                return $html;
            },
            'filter' => $searchModel && !$searchModel->isConstantly ? \yii\bootstrap\Html::activeRadioList(
                $searchModel, 'onlyNotFulfilled', [
                '0' => Yii::t('TrackerIssuesModule.views', 'All'),
                '1' => Yii::t('TrackerIssuesModule.views', 'Not fulfilled'),
                '2' => Yii::t('TrackerIssuesModule.views', 'For you'),
            ])
                : false,
        ],
    ],
]) ?>
