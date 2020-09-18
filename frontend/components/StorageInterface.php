<?php

namespace frontend\components;

use yii\web\UploadedFile;
interface StorageInterface
{
    public function saveUploadedFile(UploadedFile $file);
    public function getFile(string $filename) : string ;
    public function deleteFile(string $filename) : bool;
}