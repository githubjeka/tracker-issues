<?php
/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */

namespace tracker\controllers;

use humhub\modules\admin\components\Controller;
use humhub\modules\admin\permissions\ManageModules;
use humhub\modules\content\models\ContentTag;
use tracker\models\Document;
use tracker\models\DocumentCategory;
use tracker\models\DocumentType;
use tracker\Module;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * Configuration controller of Module Tracker issue
 */
class ConfigController extends Controller
{
    public $defaultAction = 'categories';

    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        return [['permissions' => ManageModules::class]];
    }

    /**
     * The action list of categories.
     *
     * @return string
     */
    public function actionCategories()
    {
        return $this->render('categories', [
            'dataProvider' => new ActiveDataProvider(['query' => DocumentCategory::findByType(DocumentCategory::class)]),
        ]);
    }

    /**
     * The action list of types.
     *
     * @return string
     */
    public function actionTypes()
    {
        return $this->render('types', [
            'dataProvider' => new ActiveDataProvider(['query' => DocumentType::findByType(DocumentType::class)]),
        ]);
    }

    /**
     * The action to create a type document model
     *
     * @return string
     */
    public function actionCreateType()
    {
        $documentTypeModel = new DocumentType();
        if ($this->saveTagModel($documentTypeModel)) {
            return $this->redirectToTypesPage();
        }
        return $this->renderAjax('editTypeModal', ['model' => $documentTypeModel]);
    }

    /**
     * The action to create a type document model
     *
     * @return string
     */
    public function actionCreateCategory()
    {
        $documentCategoryModel = new DocumentCategory();
        if ($this->saveTagModel($documentCategoryModel)) {
            return $this->redirectToCategoriesPage();
        }
        return $this->renderAjax('editCategoryModal', ['model' => $documentCategoryModel]);
    }

    /**
     * The action to edit the type document model
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEditType($id)
    {
        $documentTypeModel = $this->findTagModel(DocumentType::class, $id);
        if ($this->saveTagModel($documentTypeModel)) {
            return $this->redirectToTypesPage();
        }
        return $this->renderAjax('editTypeModal', ['model' => $documentTypeModel]);
    }

    /**
     * The action to edit the type document model
     *
     * @param $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionEditCategory($id)
    {
        $documentCategoryModel = $this->findTagModel(DocumentCategory::class, $id);
        if ($this->saveTagModel($documentCategoryModel)) {
            return $this->redirectToCategoriesPage();
        }
        return $this->renderAjax('editCategoryModal', ['model' => $documentCategoryModel]);
    }

    public function actionDeleteType($id)
    {
        $entryType = $this->findTagModel(DocumentType::class, $id);

        $transaction = \Yii::$app->db->beginTransaction();
        if ($entryType->delete()) {
            Document::updateAll(['type' => null], ['type' => $id]);
            $this->view->success(\Yii::t('TrackerIssuesModule.base', 'Deleted.'));
            $transaction->commit();
        } else {
            $this->view->error(\Yii::t('TrackerIssuesModule.base', 'Not deleted.'));
            $transaction->rollBack();
        }

        return $this->redirectToTypesPage();
    }

    public function actionDeleteCategory($id)
    {
        $entryType = $this->findTagModel(DocumentCategory::class, $id);

        if (Document::find()->where(['category' => $id])->exists()) {
            $this->view->error(
                \Yii::t(
                    'TrackerIssuesModule.views',
                    'Can not be deleted while documents exist for this category'
                )
            );
            // because files of documents stored in directories by category
        } else {
            if ($entryType->delete()) {
                $this->view->success(\Yii::t('TrackerIssuesModule.views', 'Deleted.'));
            } else {
                $this->view->error(\Yii::t('TrackerIssuesModule.views', 'Not deleted.'));
            }
        }

        return $this->redirectToCategoriesPage();
    }

    private function redirectToTypesPage()
    {
        return $this->htmlRedirect(URL::to(['/' . Module::getIdentifier() . '/config/types']));
    }

    private function redirectToCategoriesPage()
    {
        return $this->htmlRedirect(URL::to(['/' . Module::getIdentifier() . '/config/categories']));
    }

    private function saveTagModel(ContentTag $model)
    {
        if (!$model->load(\Yii::$app->request->post())) {
            return false;
        }
        if (!$model->save()) {
            return false;
        }
        $this->view->saved();
        return true;
    }

    /**
     * @param $className
     * @param $id
     * @return ContentTag
     * @throws NotFoundHttpException
     */
    private function findTagModel($className, $id)
    {
        /** @var ContentTag $className */
        /** @var ContentTag $contentTagModel */
        $contentTagModel = $className::findGlobal()->andWhere(['id' => $id])->one();
        if (!$contentTagModel) {
            throw new NotFoundHttpException();
        }
        return $contentTagModel;
    }
}
