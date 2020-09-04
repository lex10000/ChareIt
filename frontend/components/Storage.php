<?php

namespace frontend\components;

use Yii;
use yii\web\UploadedFile;
use yii\base\Component;
use yii\helpers\FileHelper;
class Storage extends Component implements StorageInterface
{
    private $file;
    private $filename;

    public function saveUploadedFile(UploadedFile $file)
    {
        $this->file = $file;
        $path = $this->preparePath();

        if($path && $this->file->saveAs($path)) {
            return $this->filename;
        }

    }
    public function getFile(string $filename)
    {
        return Yii::$app->params['storageUri'].$filename;
    }
    protected function preparePath()
    {
        $this->filename = $this->getFileName();

        $path = $this->getStoragePath().$this->filename;
        $path = FileHelper::normalizePath($path);
        if(FileHelper::createDirectory(dirname($path))) {
            return $path;
        }
    }

    /**
     * @return string новое название файла, с вложением в две папки рандомные
     */
    protected function getFileName()
    {
        $hash = sha1_file($this->file->tempName);

        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);

        return $name.'.'.$this->file->extension;

    }
    protected function getStoragePath()
    {
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

    /**
     * Удаляет файл с диска
     * @param string $filename имя файла из таблицы с постами
     * @return bool результат выполнения
     */
    public function deleteFile(string $filename)
    {
        return FileHelper::unlink($this->getFile($filename));
    }
}
