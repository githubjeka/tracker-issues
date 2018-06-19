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
     * @return string name to download
     */
    public function getDownloadName()
    {
        $number = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $this->document->number);
        $date = \Yii::$app->formatter->asDate($this->document->registered_at);
        $name = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $this->document->name);

        try {
            $extensions = FileHelper::getExtensionsByMimeType($this->getMimeType());
        } catch (\LogicException $e) {
            $extensions = [];
        }
        if (isset($extensions[0])) {
            $extension = $extensions[0];
        } else {
            $extension = pathinfo($this->getPath() . $this->document->file->filename, PATHINFO_EXTENSION);
        }

        return $number . ' ' . $date . ' ' . $name . '.' . $extension;
    }

    public function getPath()
    {
        $rootPath = $this->getDocumentsRootPath();
        $categoryPath = $this->getDirByCategory($this->document->categoryModel);
        $documentPath = $this->document->id;
        return $rootPath . DIRECTORY_SEPARATOR . $categoryPath . DIRECTORY_SEPARATOR . $documentPath . DIRECTORY_SEPARATOR;
    }

    public function getMimeType()
    {
        $fullPathToFile = $this->getPath() . $this->document->file->filename;
        if (!is_file($fullPathToFile)) {
            throw new \LogicException();
        }
        return FileHelper::getMimeType($fullPathToFile);
    }

    /**
     * @var string path's to move document files
     */
    private $fromPath, $toPath;

    /**
     * @param null|DocumentCategory $category
     */
    public function prepareToMoveCategory(DocumentCategory $category = null)
    {
        $this->fromPath = $this->getPath();
        $this->toPath = $this->getDocumentsRootPath() . DIRECTORY_SEPARATOR .  $this->getDirByCategory($category) . DIRECTORY_SEPARATOR . $this->document->id . '/';
    }

    /**
     * @return bool
     */
    public function moveToNewCategory()
    {
        $fromPath = $this->fromPath;
        $toPath = $this->toPath;

        if ($toPath === null || $fromPath === null) {
            return true;
        }

        try {
            FileHelper::copyDirectory($fromPath, $toPath);
        } catch (\yii\base\InvalidParamException $e) {
            \Yii::error($e->getMessage() . "Copy from $fromPath to $toPath", Module::getIdentifier());
            return false;
        }

        try {
            FileHelper::removeDirectory($fromPath);
        } catch (\yii\base\ErrorException $e) {
            \Yii::error($e->getMessage() . "Remove $fromPath", Module::getIdentifier());
            return false;
        }

        return true;
    }

    private function getDirByCategory(DocumentCategory $category = null)
    {
        if ($category === null) {
            return 'no-category';
        }

        return $category->id;
    }

    private function getDocumentsRootPath()
    {
        /** @var Module $module */
        $module = \Yii::$app->moduleManager->getModule(Module::getIdentifier());
        return FileHelper::normalizePath($module->documentRootPath);
    }
}
