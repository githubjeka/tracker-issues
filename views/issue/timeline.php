<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
use tracker\widgets\StatusIssueWidget;
use tracker\widgets\TagsWidget;
use yii\helpers\Html;

/**
 * @var $this \humhub\components\View
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \tracker\models\IssueSearch $searchModel
 * @var \humhub\modules\content\components\ContentContainerActiveRecord $contentContainer
 * @var boolean $canCreateNewIssue
 */

$timeLineAsset = \tracker\assets\TimelineAsset::register($this);

$events = [];
$dataProvider->pagination->setPageSize(300);
foreach ($dataProvider->getModels() as $model) {

    /* @var $model \tracker\models\Issue */
    if (empty($model->started_at)) {
        continue;
    }

    $firstTag = $model->getPersonalTags()->one();
    $group = '';

    if ($firstTag) {
        $group = TagsWidget::widget(['tagsModels' => [$firstTag]]);
    } else {
        $group = '<span class="label label-default">' . $model->content->getContainer()->getDisplayName() . '</span>';
    }

    $array = [
        'text' => [
            'headline' => StatusIssueWidget::widget(['status' => $model->status, 'asCheckboxIcon' => true]) .
                          ' ' .
                          Html::a(Html::encode($model->title), $model->content->getUrl()),
            'text' => StatusIssueWidget::widget(['status' => $model->status]) . ' ' . $group,
        ],
        'unique_id' => $model->id,
        'group' => $group,
        'background' => ['color' => '#708fa0'],
    ];

    $nowTime = new \DateTimeImmutable();
    $startTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $model->started_at);
    $finishTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $model->finished_at);
    $deadlineTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $model->deadline);

    if ($finishTime) {
        $array['start_date'] = [
            'year' => $finishTime->format('Y'),
            'month' => $finishTime->format('m'),
            'day' => $finishTime->format('d'),
            'hour' => $finishTime->format('H'),
            'minute' => $finishTime->format('i'),
        ];
    } elseif ($startTime > $nowTime) {
        $array['start_date'] = [
            'year' => $startTime->format('Y'),
            'month' => $startTime->format('m'),
            'day' => $startTime->format('d'),
            'hour' => $startTime->format('H'),
            'minute' => $startTime->format('i'),
        ];
    } elseif ($startTime < $nowTime && $deadlineTime) {
        $array['start_date'] = [
            'year' => $startTime->format('Y'),
            'month' => $startTime->format('m'),
            'day' => $startTime->format('d'),
            'hour' => $startTime->format('H'),
            'minute' => $startTime->format('i'),
        ];
        $array['end_date'] = [
            'year' => $deadlineTime->format('Y'),
            'month' => $deadlineTime->format('m'),
            'day' => $deadlineTime->format('d'),
            'hour' => $deadlineTime->format('H'),
            'minute' => $deadlineTime->format('i'),
        ];
    } else {
        $array['start_date'] = [
            'year' => $startTime->format('Y'),
            'month' => $startTime->format('m'),
            'day' => $startTime->format('d'),
            'hour' => $startTime->format('H'),
            'minute' => $startTime->format('i'),
        ];
    }

    $events[] = $array;
}

$json = json_encode(['events' => $events,]);
$lang = \Yii::$app->user->identity->language;
$scriptPath = \Yii::$app->assetManager->getPublishedUrl($timeLineAsset->sourcePath);
$this->registerJs("
var additionalOptions = {
    language: '$lang',
    script_path : '$scriptPath'
}
timeline = new TL.Timeline('timeline-embed', $json, additionalOptions);")
?>

<div class="panel panel-default">
    <div class="panel-body">
        <div id='timeline-embed' style="height: 700px">
        </div>
    </div>
</div>
