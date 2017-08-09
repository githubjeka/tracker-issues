<?php

namespace tracker\controllers;

use humhub\components\Controller;
use tracker\controllers\services\TagDesignator;
use tracker\models\Issue;
use tracker\models\IssueSearch;
use tracker\models\Tag;
use tracker\models\TagSearch;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends Controller
{
    public $subLayout = '@tracker/views/layouts/sub_layout_tag';

    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::class,
            ],
        ];
    }

    /**
     * Lists all Tag models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, \Yii::$app->user->id);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tag model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        $searchModel = new IssueSearch([
            'tag' => [$model->id],
            'isConstantly' => false,
        ]);

        return $this->render('view', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $searchModel->search(Yii::$app->request->get()),
        ]);
    }

    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tag(['owner_id' => \Yii::$app->user->id]);
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new Tag model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @param $id string id of issue to designate tag for him
     *
     * @return mixed
     */
    public function actionDesignate($id)
    {
        $issueModel = Issue::findOne($id);
        $tagDesignator = new TagDesignator($issueModel);

        if ($tagDesignator->load(\Yii::$app->request->post()) && $tagDesignator->save()) {
            return '{}';
        }

        return $this->renderAjax('designate', ['formModel' => $tagDesignator->getIssueForm()]);
    }

    /**
     * Updates an existing Tag model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tag model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
