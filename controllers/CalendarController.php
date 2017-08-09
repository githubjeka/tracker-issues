<?php

namespace tracker\controllers;

use tracker\controllers\services\CalendarContainer;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class CalendarController extends \humhub\components\Controller
{
    public $subLayout = '@tracker/views/layouts/sub_layout_issues';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                'only' => ['issues'],
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function actionIssues($end)
    {
        $userComponent = \Yii::$app->user;

        if ($userComponent->isGuest) {
            $this->forbidden();
        }

        $calendarContainer = new CalendarContainer();
        return $calendarContainer->getEvents($end);
    }


    public function actionIndex()
    {
        $userComponent = \Yii::$app->user;

        if ($userComponent->isGuest) {
            $this->forbidden();
        }

        return $this->render('index', [
            'contentContainer' => $userComponent->getIdentity(),
            'canCreateNewIssue' => true,
        ]);
    }
}
