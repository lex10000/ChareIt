<?php

namespace frontend\modules\user\models\forms;
use yii\base\Model;
use Yii;
class PictureForm extends Model
{
    public $picture;

    public function rules()
    {
        return [
            [
                ['picture'], 'file',
                'extensions'=> ['jpeg'],
                'checkExtensionByMimeType' =>true,
                'maxSize' => $this->getMaxFileSize(),
            ],
        ];
    }

    public function save()
    {
        return 1;
    }

    public function getMaxFileSize()
    {
        return Yii::$app->params['maxFileSize'];
    }
}