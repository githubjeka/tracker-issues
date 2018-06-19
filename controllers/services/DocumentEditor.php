<?php

namespace tracker\controllers\services;

use tracker\controllers\requests\DocumentRequest;
use tracker\models\Document;
use tracker\models\DocumentCategory;
use tracker\models\DocumentFileEntity;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentEditor extends \yii\base\Model
{
    /**
     * This object will be used for operations on document files if it need.
     *
     * @var null|DocumentFileEntity
     */
    public $documentFileEntity;

    /** @var DocumentRequest */
    private $requestForm;

    /** @var Document */
    private $document;

    public function __construct(Document $document, array $config = [])
    {
        parent::__construct($config);
        $this->document = $document;
        if ($this->document->isNewRecord) {
            throw new \LogicException('Document should be created before using this service');
        }
        $this->requestForm = new DocumentRequest();
        $this->requestForm->number = $this->document->number;
        $this->requestForm->name = $this->document->name;
        $this->requestForm->description = $this->document->description;
        $this->requestForm->registeredAt = date('Y-m-d', $this->document->registered_at);
        $this->requestForm->from = $this->document->from;
        $this->requestForm->to = $this->document->to;
        $this->requestForm->type = $this->document->type;
        $this->requestForm->category = $this->document->category;
    }

    /**
     * @return DocumentRequest
     */
    public function getDocumentForm()
    {
        return $this->requestForm;
    }

    public function load($datum, $formName = null)
    {
        return $this->requestForm->load($datum, $formName);
    }

    public function save()
    {
        if (!$this->requestForm->validate([
            'number',
            'name',
            'description',
            'registeredAt',
            'type',
            'from',
            'to',
            'category',
        ])) {
            return false;
        }

        $transaction = \Yii::$app->db->beginTransaction();

        $this->document->name = $this->requestForm->name;
        $this->document->number = $this->requestForm->number;
        $this->document->description = $this->requestForm->description;
        $this->document->to = $this->requestForm->to;
        $this->document->from = $this->requestForm->from;
        $this->document->type = $this->requestForm->type;

        $registeredAtDateObj = \DateTime::createFromFormat('Y-m-d', $this->requestForm->registeredAt);
        if ($registeredAtDateObj === false) {
            $transaction->rollBack();
            throw new \LogicException('registeredAt should Y-m-d');
        }
        $this->document->registered_at = $registeredAtDateObj->setTime(0, 0)->format('U');

        if (($this->documentFileEntity === null)) {
            $docFile = new DocumentFileEntity($this->document);
        } else {
            $docFile = $this->documentFileEntity;
        }

        if ($this->document->category !== $this->requestForm->category) {
            $docFile->prepareToMoveCategory(DocumentCategory::findOne($this->requestForm->category));
            $this->document->category = $this->requestForm->category;
        }

        if (!$this->document->save()) {
            $transaction->rollBack();
            throw new \LogicException(json_encode($this->document->errors));
        }

        if (!$docFile->moveToNewCategory()) {
            $transaction->rollBack();
            throw new \LogicException('The document file can not be moved to a new path');
        }

        $transaction->commit();

        $this->document->refresh();

        return $this->document;
    }
}
