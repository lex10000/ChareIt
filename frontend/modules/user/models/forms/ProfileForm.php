<?php
namespace frontend\modules\user\models\forms;

use frontend\components\Storage;
use frontend\modules\user\models\User;
use yii\base\Model;
use Yii;

class ProfileForm extends Model
{
    public $picture;
    public $about;

    public function rules()
    {
        return [
            [
                ['picture'], 'file',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'skipOnEmpty' => true,
                'checkExtensionByMimeType' => true,
                'maxSize' => Yii::$app->params['maxFileSize']
            ],
            ['about', 'string', 'max' => 255]
        ];
    }

    public function attributeLabels()
    {
        return [
            'picture' => 'аватарка',
            'about' => 'Несколько слов о себе'
        ];
    }

    /**
     * Сохранить настройки профиля пользователя. Если меняется аватарка (и если она была создана до этого) - то удалить
     * файлы предыдущей аватарки.
     * @return bool
     */
    public function save()
    {
        if ($this->validate()) {
            $user = User::findById(Yii::$app->user->getId());
            if($this->picture) {
                if($user->picture){
                    Yii::$app->storage->deleteFile('profile_avatars/'.$user->picture);
                    Yii::$app->storage->deleteFile('profile_avatars/thumbnails/'.$user->picture);
                }
                $user->picture = Yii::$app->storage->saveUploadedFile($this->picture, Storage::FILETYPE_AVATAR, true);
            }
            $user->about = $this->about;
            return $user->save();
        }
    }
}