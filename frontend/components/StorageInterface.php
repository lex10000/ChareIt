<?php

namespace frontend\components;

use yii\web\UploadedFile;
interface StorageInterface
{
    function saveUploadedFile(UploadedFile $file, $type, $thumbnail = false);
    function deleteFile(string $filename) : bool;
    function createThumbnail(string $path) : void;
}