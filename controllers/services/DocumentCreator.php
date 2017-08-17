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
 * Main service to provide public API for manipulation with Document model.
 *
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentCreator extends \yii\base\Model
{
    /** @var DocumentRequest */
    private $requestForm;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->requestForm = new DocumentRequest();
    }

    /**
     * @return DocumentRequest
     */
    public function getDocumentForm()
    {
        return $this->requestForm;
    }

    /**
     * Populates the document request model with input user data.
     *
     * @param array $datum
     * @param null $formName
     *
     * @return bool
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

    /**
     * Public API to create document
     *
     * @return false|Document
     */
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

        $registeredAtDateObj = \DateTime::createFromFormat('Y-m-d', $this->requestForm->registeredAt);
        if ($registeredAtDateObj === false) {
            $transaction->rollBack();
            throw new \LogicException('registeredAt should Y-m-d');
        }
        $documentModel->registered_at = $registeredAtDateObj->setTime(0, 0)->format('U');

        if (!$documentModel->save()) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($documentModel->errors));
        }

        try {
            $this->addFileToDocument($documentModel);
        } catch (\LogicException $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\RuntimeException $e) {
            $transaction->rollBack();
            throw $e;
        }

        try {
            $this->addReceiversTo($documentModel);
        } catch (\LogicException $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\RuntimeException $e) {
            $transaction->rollBack();
            throw $e;
        }

        $transaction->commit();
        $documentModel->refresh();

        return $documentModel;
    }

    /**
     * Public API to add new receivers to the document
     *
     * @param Document $document
     *
     * @return false|Document
     */
    public function addReceiversTo(Document $document)
    {
        if (!$this->requestForm->validate(['receivers'])) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        foreach ($this->requestForm->receivers as $userGuid) {
            $user = \humhub\modules\user\models\User::findOne(['guid' => $userGuid]);

            if ($user === null || DocumentReceiver::find()
                    ->where(['user_id' => $user->id, 'document_id' => $document->id])
                    ->exists()) {
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

    /**
     * Public API to add new file to the document.
     * This file will be main by download in document model.
     * Old files by the document just storing in database to ability revert changes.
     *
     * @param Document $documentModel
     * TODO: fix bug to save files by same naming
     *
     * @return false|Document
     */
    public function addFileToDocument(Document $documentModel)
    {
        if (!$this->requestForm->validate(['file'])) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $oldMainDocument = $documentModel->file;

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

        if ($oldMainDocument) {
            $oldMainDocument->is_show = false;
            if (!$oldMainDocument->save()) {
                $transaction->rollBack();
                throw new \LogicException(json_encode($oldMainDocument->errors));
            }
        }

        $transaction->commit();

        return $documentModel;
    }
}
