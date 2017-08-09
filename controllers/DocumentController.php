<?php

namespace tracker\controllers;

use humhub\components\Controller;
use tracker\controllers\services\DocumentCreator;
use tracker\models\Document;
use tracker\models\DocumentReceiver;
use tracker\Module;
use tracker\permissions\AddDocument;
use tracker\permissions\AddReceiversToDocument;
use Yii;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * DocumentController implements the CRUD actions for Document model.
 */
class DocumentController extends Controller
{
    public $subLayout = '@tracker/views/layouts/sub_layout_issues';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }


    /**
     * Displays a single Document model.
     *
     * @param integer $id
     *
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Document model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionCreate()
    {
        if (!\Yii::$app->user->can(new AddDocument())) {
            $this->forbidden();
        }

        $documentCreator = new DocumentCreator();
        $request = \Yii::$app->request;

        if ($documentCreator->load($request->post()) && $document = $documentCreator->create()) {
            return $this->redirect(['view', 'id' => $document->id]);
        }

        return $this->renderAjax('create', [
            'documentRequest' => $documentCreator->getDocumentForm(),
        ]);
    }

    public function actionDownload($id)
    {
        $document = Document::find()->readable()->byId($id)->one();
        if ($document === null) {
            throw new ForbiddenHttpException();
        }
        /** @var Module $module */
        $module = \Yii::$app->getModule(Module::getIdentifier());
        $category = isset(Document::categories()[$document->category]) ? $document->category : 'no-category';
        $path = $module->documentRootPath . $category . '/' . $document->id . '/';

        $receiver = DocumentReceiver::findOne([
            'view_mark' => 0,
            'user_id' => \Yii::$app->user->id,
            'document_id' => $document->id,
        ]);
        if ($receiver !== null) {
            $receiver->viewed_at = date('Y-m-d H:i');
            $receiver->view_mark = 1;
            if (!$receiver->save(true, ['viewed_at', 'view_mark'])) {
                \Yii::warning(json_encode($receiver->errors));
            }
        }

        return Yii::$app
            ->response
            ->sendContentAsFile(
                file_get_contents($path . $document->file->filename),
                $document->file->filename,
                ['inline' => true,]
            );
    }

    public function actionChangeCategory($id)
    {
        // TODO
    }

    public function actionToAddReceivers($id)
    {
        $document = Document::find()->byId($id)->one();

        if ($document === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if (!\Yii::$app->user->can(new AddReceiversToDocument()) &&
            $document->created_by !== Yii::$app->user->id) {
            $this->forbidden();
        }

        $documentCreator = new DocumentCreator();

        if (Yii::$app->request->isPost) {
            if ($documentCreator->load(Yii::$app->request->post())) {
                $document = $documentCreator->addReceiversTo($document);
                return $this->redirect(['view', 'id' => $document->id]);
            }
        }

        return $this->renderAjax('to_add_receivers_document', [
            'requestModel' => $documentCreator->getDocumentForm(),
            'actionUrl' => \yii\helpers\Url::to([
                '/' . Module::getIdentifier() . '/document/to-add-receivers',
                'id' => $document->id,
            ]),
        ]);
    }

    /**
     * Finds the Document model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     *
     * @return Document the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Document::find()->readable()->byId($id)->one();

        if ($model === null) {

            if (!\Yii::$app->user->isGuest) {
                $model = Document::find()->byCreator(Yii::$app->user->identity)->byId($id)->one();
            }

            if ($model === null) {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }

        return $model;
    }
}
