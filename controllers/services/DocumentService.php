<?php

namespace tracker\controllers\services;


use tracker\models\Document;

class DocumentService
{
    /**
     * @var Document ID of User
     */
    private $document;

    /**
     * DocumentReceiver constructor.
     *
     * @param Document $document
     */
    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * Marks document as the observed for a receiver.
     *
     * @param string $userId ID of User
     *
     * @return $this
     */
    public function markAsObserved($userId)
    {
        $receiver = \tracker\models\DocumentReceiver::findOne([
            'view_mark' => 0,
            'user_id' => $userId,
            'document_id' => $this->document->id,
        ]);

        if ($receiver === null) {
            return $this;
        }

        $receiver->viewed_at = date('Y-m-d H:i');
        $receiver->view_mark = 1;

        if (!$receiver->save(true, ['viewed_at', 'view_mark'])) {
            \Yii::warning(json_encode($receiver->errors));
        }

        return $this;
    }
}
