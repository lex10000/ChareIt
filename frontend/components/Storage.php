<?php
declare(strict_types = 1);
namespace frontend\components;

use Imagick;
use Yii;
use yii\web\UploadedFile;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\imagine\Image;
use claviska\SimpleImage;

class Storage extends Component implements StorageInterface
{
    /**
     * @var UploadedFile файл переданный из формы
     */
    private $file;

    /**
     * @var string имя файла (включая подпапки)
     */
    private $filename;

    /**
     * Сохраняет файл из формы на диск, а так же возвращает новое имя файла для записи в бд.
     * Так же создает уменьшенную копию изображения, и возвращает имя файла (для показа в ленте)
     * @param UploadedFile $file файл из формы
     * @param bool $thumbnail создавать уменьшенную копию
     * @return string|null имя файла (включая подпапки)
     */
    public function saveUploadedFile(UploadedFile $file, $thumbnail = false) : ?string
    {
        $this->file = $file;
        $path = $this->preparePath();

        if($path && $this->file->saveAs($path)) {
            if($thumbnail) $this->createThumbnail($path);
            return $this->filename;
        }
    }

    /**
     * @param string $filename название файла (из таблицы users)
     * @return string относительный путь до файла
     */
    public function getFile(string $filename) : string
    {
        return Yii::$app->params['storageUri'].$filename;
    }

    /**
     * @return string|null строка с абсолютным путем для файла.
     */
    protected function preparePath() : ?string
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
    protected function getFileName() : string
    {
        $hash = sha1_file($this->file->tempName).time();

        $name = substr_replace($hash, '/', 2, 0);
        $name = substr_replace($name, '/', 5, 0);

        return $name.'.'.$this->file->extension;
    }

    /**
     * @return string путь к папке с загружаемыми файлами
     */
    protected function getStoragePath() : string
    {
        return Yii::getAlias(Yii::$app->params['storagePath']);
    }

    /**
     * Удаляет файл с диска
     * @param string $filename имя файла из таблицы с постами
     * @return bool результат выполнения
     */
    public function deleteFile(string $filename) : bool
    {
        return FileHelper::unlink($this->getFile($filename));
    }

    /**
     * Создает уменьшенное изображение (для показа в ленте)
     * TODO: переделать метод под preparePath()
     * @param string $path путь до исходного изображения
     */
    private function createThumbnail($path)
    {
        $thumbnail_path = $this->getStoragePath().'thumbnails/'.$this->filename;
        $thumbnail_path = FileHelper::normalizePath($thumbnail_path);

        if(FileHelper::createDirectory(dirname($thumbnail_path))) {
            Image::resize($path, 1000, null, false)
                ->save($thumbnail_path);
        }
    }
}
