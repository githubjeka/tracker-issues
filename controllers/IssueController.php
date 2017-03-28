<?php

namespace tracker\controllers;

use humhub\modules\content\components\ContentContainerController;
use tracker\models\Assignee;
use tracker\models\Issue;
use yii\web\NotFoundHttpException;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class IssueController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'stream' => [
                'class' => StreamAction::className(),
                'contentContainer' => $this->contentContainer,
            ],
            'dashboard-stream' => [
                'class' => DashboardStreamAction::className(),
            ],
        ];
    }

    public $hideSidebar = false;

    public function actionShow()
    {
        $issues = Issue::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->all();

        return $this->render('show', [
            'issues' => $issues,
            'contentContainer' => $this->contentContainer,
            'canCreateNewIssue' => $this->canUserDo(new \tracker\permissions\CreateIssue()),
        ]);
    }

    public function actionCreate()
    {
        if (!$this->canUserDo(new \tracker\permissions\CreateIssue())) {
            $this->forbidden();
        }

        $issueCreator = new IssueCreator();

        if ($issueCreator->load(\Yii::$app->request->post()) && $model = $issueCreator->create()) {
            return $this->redirect($model->content->getUrl());
        }

        $issueCreator->createDraft($this->contentContainer);

        return $this->renderAjax('create', ['issueForm' => $issueCreator->getIssueForm()]);
    }

    public function actionEdit($id)
    {
        /** @var Issue|null $issue */
        $issue = Issue::find()
            ->contentContainer($this->contentContainer)
            ->readable()
            ->where([Issue::tableName() . '.id' => $id,])
            ->one();

        if ($issue === null) {
            throw new NotFoundHttpException('Issue not founded.');
        }

        if (!$this->canUserDo(new \tracker\permissions\EditIssue())) {
            $this->forbidden();
        }

        $issueEditor = new IssueEditor($issue);

        if ($issueEditor->load(\Yii::$app->request->post())) {
            if ($issue = $issueEditor->save()) {
                return $this->renderAjaxContent($issue->getWallOut());
            }
        }

        return $this->renderAjax('edit', ['issueForm' => $issueEditor->getIssueForm()]);
    }

    public function actionMarkAdopted($id)
    {
        $assignee = $this->findAssignee($id);
        $assignee->view_mark = 1;
        $assignee->save();

        return $this->renderAjaxContent($assignee->issue->getWallOut());
    }

    public function actionMarkDone($id)
    {
        $assignee = $this->findAssignee($id);
        $assignee->finish_mark = 1;
        $assignee->save();

        return $this->renderAjaxContent($assignee->issue->getWallOut());
    }

    protected function findAssignee($id)
    {
        $assignee = Assignee::findOne(['id' => $id]);

        if ($assignee === null) {
            throw new NotFoundHttpException();
        }

        if ((int)$assignee->user_id !== (int)\Yii::$app->user->id) {
            $this->forbidden();
        }

        return $assignee;
    }

    /**
     * @param $permission
     * @param array $params
     * @param bool $allowCaching
     *
     * @return bool
     */
    protected function canUserDo($permission, $params = [], $allowCaching = true)
    {
        return $this->contentContainer
            ->getPermissionManager()
            ->can($permission, $params, $allowCaching);
    }
}
