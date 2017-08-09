<?php

namespace tracker\controllers;

use humhub\components\Controller;
use tracker\controllers\actions\DashboardStreamAction;
use tracker\enum\IssueStatusEnum;
use tracker\models\DocumentSearch;
use tracker\models\IssueSearch;
use tracker\Module;
use tracker\permissions\AddDocument;
use tracker\permissions\CreateIssue;
use Yii;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DashboardController extends Controller
{
    public $subLayout = '@tracker/views/layouts/sub_layout_issues';

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

        $searchModel = new IssueSearch([
            'nullIfError' => true,
            'status' => IssueStatusEnum::TYPE_WORK,
            'isConstantly' => false,
        ]);

        return $this->render('/issue/show', [
            'dataProvider' => $searchModel->search(\Yii::$app->request->get()),
            'searchModel' => $searchModel,
            'contentContainer' => $userComponent->getIdentity(),
            'canCreateNewIssue' => true,
        ]);
    }

    /**
     * Lists all Document models.
     *
     * @return mixed
     */
    public function actionDocuments()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/document/index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionTimeline()
    {
        $userComponent = \Yii::$app->user;

        if ($userComponent->isGuest) {
            $this->forbidden();
        }

        $searchModel = new IssueSearch([
            'isConstantly' => false,
            'status' => [
                \tracker\enum\IssueStatusEnum::TYPE_WORK,
                \tracker\enum\IssueStatusEnum::TYPE_FINISHED,
            ],
        ]);

        return $this->render('/issue/timeline', [
            'dataProvider' => $searchModel->search(\Yii::$app->request->get()),
            'searchModel' => $searchModel,
            'contentContainer' => $userComponent->getIdentity(),
            'canCreateNewIssue' => true,
        ]);
    }

    public function actionToCreateIssue()
    {
        if (!\Yii::$app->user->can(new CreateIssue())) {
            $this->forbidden();
        }
        return $this->renderAjax('to_create_issue', [
            'actionUrl' => \yii\helpers\Url::to([
                '/' . Module::getIdentifier() . '/issue/create',
            ]),
        ]);
    }

    public function actionToCreateDocument()
    {
        if (!\Yii::$app->user->can(new AddDocument())) {
            $this->forbidden();
        }

        return $this->renderAjax('to_create_document', [
            'actionUrl' => \yii\helpers\Url::to([
                '/' . Module::getIdentifier() . '/document/create',
            ]),
        ]);
    }

    public function actionFiles()
    {
        return $this->render('elfinder');
    }
}
