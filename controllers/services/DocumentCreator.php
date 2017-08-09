<?php

namespace tracker\controllers\services;

use humhub\modules\content\components\ContentContainerActiveRecord;
use tracker\controllers\requests\DocumentRequest;
use tracker\enum\ContentVisibilityEnum;
use tracker\enum\IssuePriorityEnum;
use tracker\enum\IssueStatusEnum;
use tracker\models\Document;
use tracker\models\DocumentIssue;
use tracker\models\DocumentReceiver;
use tracker\Module;
use tracker\notifications\DocumentShared;
use yii\web\UploadedFile;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentCreator extends \yii\base\Model
{
    /** @var DocumentRequest */
    private $requestForm;
    private $container;

    public function __construct(ContentContainerActiveRecord $container, $config = [])
    {
        if (!($container instanceof \humhub\modules\space\models\Space)) {
            throw new \LogicException('Content container should be one of Space');
        }

        $this->requestForm = new DocumentRequest();
        $this->container = $container;
        parent::__construct($config);
    }

    /** @var Document */
    protected $documentModel;

    public function getDocumentForm()
    {
        return $this->requestForm;
    }

    /**
     * @param array $datum
     * @param null $formName
     *
     * @return bool
     * @see Model::load()
     */
    public function load($datum, $formName = null)
    {
        $result = $this->requestForm->load($datum, $formName);

        if (!$result) {
            return false;
        }

        $this->requestForm->file = UploadedFile::getInstanceByName(
            $formName === '' ? 'file' : $formName . '["file"]'
        );
        return true;
    }

    public function create()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $model = new Document();
        $model->content->notifyUsersOfNewContent = false;
        $model->content->setContainer($this->container);
        $model->name = $this->requestForm->name;
        $model->number = $this->requestForm->number;
        $model->description = $this->requestForm->description;
        $model->to = $this->requestForm->to;
        $model->from = $this->requestForm->from;
        $model->category = $this->requestForm->category;
        $model->type = $this->requestForm->type;
        $model->file = $this->requestForm->file->name;

        if (!$model->save()) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($model->errors));
        }

        /** @var Module $module */
        $module = \Yii::$app->moduleManager->getModule(Module::getIdentifier());
        $category = isset(Document::categories()[$model->category]) ? $model->category : 'no-category';
        $path = $module->documentRootPath . $category . '/' . $model->id . '/';

        if (!file_exists($path) && !mkdir($path, 0774, true)) {
            $transaction->rollBack();
            throw new \RuntimeException('Can not created ' . realpath($path));
        };

        if (!$this->requestForm->file->saveAs($path . $model->file)) {
            $transaction->rollBack();
            throw new \RuntimeException('Can not saved the file ' . $model->file . ' in ' . $path);
        }

        $this->addReceiversTo($model);

        $transaction->commit();

        return $model;
    }

    public function addReceiversTo(Document $document)
    {
        if (!$this->requestForm->validate(['receivers'])) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        foreach ($this->requestForm->receivers as $userGuid) {
            $user = \humhub\modules\user\models\User::findOne(['guid' => $userGuid]);
            // TODO User validate or filters
            if (DocumentReceiver::find()->where(['user_id' => $user->id, 'document_id' => $document->id])->exists()) {
                continue;
            }
            $receiver = new DocumentReceiver();
            $receiver->user_id = $user->id;
            $receiver->document_id = $document->id;
            $receiver->created_at = date('Y-m-d H:i');
            if (!$receiver->save()) {
                $transaction->rollBack();
                throw new \LogicException(json_encode($receiver->errors));
            }

            $issueCreator = new IssueCreator();
            $issueModel = $issueCreator->createDraft($user);
            $issueModel->content->updateAttributes(['created_by' => $user->id]);

            $issueCreator->load([
                'title' => $document->name,
                'description' => $document->description,
                'visibility' => ContentVisibilityEnum::TYPE_PRIVATE,
                'status' => IssueStatusEnum::TYPE_WORK,
                'startedDate' => date('Y-m-d'),
                'startedTime' => date('H:i'),
                'deadlineDate' => date('Y-m-d'),
                'deadlineTime' => '23:59',
                'priority' => IssuePriorityEnum::TYPE_URGENT,
            ], '');

            $issueModel = $issueCreator->create();
            if ($issueModel === false) {
                $transaction->rollBack();
                throw new \LogicException(json_encode($issueCreator->getIssueForm()->errors));
            }

            $link = new DocumentIssue([
                'document_id' => $document->id,
                'issue_id' => $issueModel->id,
            ]);

            if ($link->save() === false) {
                $transaction->rollBack();
                throw new \LogicException(json_encode($link->errors));
            }

            $notification = new DocumentShared();
            $notification->source = $document;
            $notification->originator = \Yii::$app->user->identity;
            $notification->send($user);
        }

        $transaction->commit();

        return $document;
    }
}
