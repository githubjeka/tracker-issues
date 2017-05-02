<?php

namespace tracker\controllers;

use humhub\components\Controller;
use tracker\enum\IssueStatusEnum;
use tracker\models\IssueSearch;
use tracker\Module;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DashboardController extends Controller
{
    public function actions()
    {
        return [
            'stream' => [
                'class' => DashboardStreamAction::className(),
            ],
        ];
    }

    public function actionIssues()
    {
        $userComponent = \Yii::$app->user;

        if ($userComponent->isGuest) {
            $this->forbidden();
        }

        $this->layout = 'main';
        $searchModel = new IssueSearch(['status' => IssueStatusEnum::TYPE_WORK]);

        return $this->render('/issue/show', [
            'dataProvider' => $searchModel->search(\Yii::$app->request->get()),
            'searchModel' => $searchModel,
            'contentContainer' => $userComponent->getIdentity(),
            'canCreateNewIssue' => true,
        ]);
    }

    public function actionToCreateIssue()
    {
        return $this->renderAjax('to_create_issue', [
            'actionUrl' => \yii\helpers\Url::to([
                '/' . Module::getIdentifier() . '/issue/create',
            ]),
        ]);
    }
}
