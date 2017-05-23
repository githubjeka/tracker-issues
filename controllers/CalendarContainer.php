<?php

namespace tracker\controllers;

use tracker\models\IssueSearch;
use tracker\widgets\TagsWidget;
use yii\base\Object;
use yii\helpers\Html;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class CalendarContainer extends Object
{
    /** @var IssueSearch */
    private $searchModel;

    public function init()
    {
        parent::init();

        $this->searchModel = new IssueSearch([
            'nullIfError' => true,
            'status' => [
                \tracker\enum\IssueStatusEnum::TYPE_WORK,
            ],
        ]);
    }

    public function getEvents($endDate)
    {
        $this->searchModel->load(['endStartedDate' => $endDate], '');
        $dataProvider = $this->searchModel->search([]);
        $dataProvider->pagination = false;

        if ($dataProvider->getTotalCount() === 0) {
            return [];
        }

        $events = [];

        $endDateObject = \DateTimeImmutable::createFromFormat('Y-m-d', $endDate);

        foreach ($dataProvider->getModels() as $model) {

            /* @var $model \tracker\models\Issue */
            if (empty($model->started_at)) {
                continue;
            }

            $title = Html::encode($model->title);

            if ($model->personalTags) {
                $title = TagsWidget::widget(['tagsModels' => $model->personalTags]) . ' | ' . $title;
            }
            $title = '<span class="label label-default">' . $model->content->getContainer()->getDisplayName() .
                     '</span> | ' . $title;

            $array = [
                'title' => $title,
                'url' => $model->content->getUrl(),
                'color' => isset($model->content->getContainer()->color) ? $model->content->getContainer()->color : '#337ab7',
            ];

            $nowTime = new \DateTimeImmutable();
            $startTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $model->started_at);
            $deadlineTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $model->deadline);

            if ($deadlineTime) {
                $array['start'] = $startTime->format('c');
                $array['end'] = $deadlineTime->format('c');
                $array['allDay'] = $endDateObject < $deadlineTime;
            } else {
                $array['start'] = $nowTime->format('c');
                $array['allDay'] = true;
            }

            $events[] = $array;
        }

        return $events;
    }
}
