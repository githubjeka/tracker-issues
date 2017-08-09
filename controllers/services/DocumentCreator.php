<?php

namespace tracker\controllers\services;

use tracker\controllers\requests\DocumentRequest;
use tracker\enum\ContentVisibilityEnum;
use tracker\enum\IssuePriorityEnum;
use tracker\enum\IssueStatusEnum;
use tracker\models\Document;
use tracker\models\DocumentFile;
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

    public function __construct($config = [])
    {
        $this->requestForm = new DocumentRequest();
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

        if ($formName === null) {
            $formName = $this->requestForm->formName();
        }

        $this->requestForm->file = UploadedFile::getInstanceByName(
            $formName === '' ? 'file' : $formName . '[file]'
        );

        return true;
    }

    public function create()
    {
        if (!$this->requestForm->validate()) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $documentModel = new Document();
        $documentModel->name = $this->requestForm->name;
        $documentModel->number = $this->requestForm->number;
        $documentModel->description = $this->requestForm->description;
        $documentModel->to = $this->requestForm->to;
        $documentModel->from = $this->requestForm->from;
        $documentModel->category = $this->requestForm->category;
        $documentModel->type = $this->requestForm->type;
        $documentModel->created_by = \Yii::$app->user->id;
        $documentModel->created_at = time();

        if (!$documentModel->save()) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($documentModel->errors));
        }

        $fileModel = new DocumentFile();
        $fileModel->document_id = $documentModel->id;
        $fileModel->filename = $this->requestForm->file->name;
        $fileModel->created_by = \Yii::$app->user->id;
        $fileModel->created_at = time();

        if (!$fileModel->save()) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($fileModel->errors));
        }

        /** @var Module $module */
        $module = \Yii::$app->moduleManager->getModule(Module::getIdentifier());
        $category = isset(Document::categories()[$documentModel->category]) ? $documentModel->category : 'no-category';
        $path = $module->documentRootPath . $category . '/' . $documentModel->id . '/';

        if (!file_exists($path) && !mkdir($path, 0774, true)) {
            $transaction->rollBack();
            throw new \RuntimeException('Can not created ' . realpath($path));
        };

        if (!$this->requestForm->file->saveAs($path . $fileModel->filename)) {
            $transaction->rollBack();
            throw new \RuntimeException('Can not saved the file ' . $fileModel->filename . ' in ' . $path);
        }

        $this->addReceiversTo($documentModel);

        $transaction->commit();

        return $documentModel;
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
