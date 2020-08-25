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
            ['name', 'string', 'min' => 3, 'max' => 255, 'tooShort' => 'Введите как минимум 3 символа для названия'],
            ['name', 'required', 'message' => 'Вы не ввели ничего'],
            ['name', 'trim']
        ];
    }

    public function saveChecklist($user_id)
    {
        if (!$this->validate()) {
            return null;
        }
        $model = new Checklist();
        $model->name = $this->name;
        $model->user_id = $user_id;
       return $model->save();
    }
}