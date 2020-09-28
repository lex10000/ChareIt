<?php
declare(strict_types=1);
namespace frontend\modules\insta\models\forms;

use frontend\components\Storage;
use yii\base\Model;
use Yii;
use frontend\modules\insta\models\Post;
use yii\helpers\Html;

class PostForm extends Model
{
    const MAX_DESCRIPTION_SIZE = 255;
    const MIN_DESCRIPTION_SIZE = 2;

    public $picture;
    public $description;
    private $user_id;

    public function __construct(int $user_id)
    {
        $this->user_id = $user_id;
        parent::__construct();
    }

    public function rules()
    {
        return [
            [['picture', 'description'], 'required'],
            [
                ['picture'], 'file',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'skipOnEmpty' => false,
                'checkExtensionByMimeType' => true,
                'maxSize' => $this->getMaxFileSize()],
            [
                ['description'], 'string', 'max' => self::MAX_DESCRIPTION_SIZE, 'min' => self::MIN_DESCRIPTION_SIZE
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Описание',
            'picture' => 'Фотография'
        ];
    }

    public function save() : ?Post
    {
        if ($this->validate()) {
            $post = new Post();
            $post->description = $this->description;
            $post->created_at = time();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture, Storage::FILETYPE_POST, true);
            $post->user_id = $this->user_id;
            return $post->save(false) ? $post : null;
        } else return null;
    }


    /**
     * @return int максимальный размер для загружаемой картинки (байт)
     */
    private function getMaxFileSize() : int
    {
        return Yii::$app->params['maxFileSize'];
    }
}