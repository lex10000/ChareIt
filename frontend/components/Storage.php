<?php
declare(strict_types = 1);
namespace frontend\components;

use Imagine\Image\ManipulatorInterface;
use Yii;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\base\Component;
use yii\helpers\FileHelper;
use yii\imagine\Image;

/**
 * Class Storage компонент для загрузки изображений (аватарки и посты)
 * @package frontend\components
 */
class Storage extends Component implements StorageInterface
{
    const FILETYPE_POST = 'post';
    const FILETYPE_AVATAR = 'avatar';

    /** @var UploadedFile файл переданный из формы */
    private $file;

    /** @var string имя файла (включая подпапки) */
    private $filename;

    /** @var string */
    private $filetype;

    /**
     * Сохраняет файл из формы на диск, а так же возвращает новое имя файла для записи в бд.
     * Так же создает уменьшенную копию изображения, и возвращает имя файла.
     * @param UploadedFile $file файл из формы
     * @param string $type тип загружаемого файла
     * @param bool $thumbnail создавать уменьшенную копию
     * @return string|null имя файла (включая подпапки)
     * @throws Exception
     */
    public function saveUploadedFile(UploadedFile $file, $type, $thumbnail = false) : ?string
    {
        $this->file = $file;
        $this->filetype = $type;

        $path = $this->preparePath();

        if($path && $this->file->saveAs($path)) {
            if($thumbnail) $this->createThumbnail($path);
            return $this->filename;
        }
    }

    /**
     * @return string|null строка с абсолютным путем для файла.
     * @throws Exception
     */
    protected function preparePath() : ?string
    {
        $this->filename = $this->getFileName();
        $path = $this->getStoragePath().$this->filename;
        $path = FileHelper::normalizePath($path);
        return FileHelper::createDirectory(dirname($path)) ? $path : null;
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
     * @return string путь к папке с загружаемыми файлами (в зависимости от типа файла
     */
    protected function getStoragePath() : string
    {
        return Yii::getAlias(Yii::$app->params["storagePath$this->filetype"]);
    }

    /**
     * Удаляет файл с диска
     * @param string $filename имя файла из таблицы с постами
     * @return bool результат выполнения
     */
    public function deleteFile(string $filename) : bool
    {
        return FileHelper::unlink($filename);
    }

    /**
     * Создает уменьшенное изображение
     * @param string $path путь до исходного изображения
     * @throws Exception
     */
    public function createThumbnail(string $path) :void
    {
        $thumbnail_path = $this->getStoragePath().'thumbnails/'.$this->filename;
        $thumbnail_path = FileHelper::normalizePath($thumbnail_path);
        if(FileHelper::createDirectory(dirname($thumbnail_path))) {
            $image = Image::resize($path, 1200, 1200, true)
                ->save($thumbnail_path);
        }
    }
}