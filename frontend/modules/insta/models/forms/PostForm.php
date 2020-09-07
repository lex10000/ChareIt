<?php

namespace frontend\modules\insta\models\forms;

use yii\base\Model;
use Yii;
use frontend\modules\insta\models\Post;

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

    public function save()
    {
        if ($this->validate()) {
            $post = new Post();
            $post->description = $this->description;
            $post->created_at = time();
            $post->filename = Yii::$app->storage->saveUploadedFile($this->picture);
            $post->user_id = $this->user_id;
            return $post->save(false) ? $post->id : false;
        }
    }


    public function getMaxFileSize()
    {
        return Yii::$app->params['maxFileSize'];
    }

}