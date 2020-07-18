<?php


namespace frontend\modules\checklist\models;

use yii\base\Model;
use frontend\modules\checklist\models\Checklist;

class ChecklistForm extends Model
{
    public $name;

    public function rules()
    {
        return [
            ['name', 'string', 'min' => 3, 'max' => 255],
            ['name', 'required'],
            ['name', 'trim']
        ];
    }

    public function saveChecklist()
    {
        if (!$this->validate()) {
            return null;
        }
        $model = new Checklist();
        $model->name = $this->name;
        $model->user_id = \Yii::$app->user->getId();
       return $model->save();
    }
}