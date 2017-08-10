<?php

namespace tracker\models;

use tracker\Module;
use yii\helpers\FileHelper;

/**
 * @author Evgeniy Tkachenko <et.coder@gmail.com>
 */
class DocumentFileEntity
{
    private $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    /**
     * @return string name to dow
     */
    public function getDownloadName()
    {
        $number = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $this->document->number);
        $date = \Yii::$app->formatter->asDate($this->document->registered_at);
        $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $this->document->name);

        $extensions = FileHelper::getExtensionsByMimeType($this->getMimeType());
        if (isset($extensions[0])) {
            $extension = $extensions[0];
        } else {
            $extension = pathinfo($this->getPath() . $this->document->file->filename, PATHINFO_EXTENSION);
        }

        return $number . ' ' . $date . ' ' . $name . '.' . $extension;
    }

    public function getPath()
    {
        /** @var Module $module */
        $module = \Yii::$app->getModule(Module::getIdentifier());
        if (isset(Document::categories()[$this->document->category])) {
            $category = $this->document->category;
        } else {
            $category = 'no-category';
        }
        return $module->documentRootPath . $category . '/' . $this->document->id . '/';
    }

    public function getMimeType()
    {
        return FileHelper::getMimeType($this->getPath() . $this->document->file->filename);
    }
}