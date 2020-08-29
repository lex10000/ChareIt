<?php


namespace frontend\modules\checklist\models;

use yii\base\Model;
use frontend\modules\checklist\models\ChecklistItems;

class ChecklistItemForm extends Model
{
    public $name;
    public $checklist_id;

    public function rules()
    {
        return [
            ['name', 'string', 'min' => 3, 'max' => 255, 'tooShort' => 'Введите как минимум 3 символа для названия'],
            ['name', 'required', 'message' => 'Вы не ввели ничего'],
            ['name', 'trim']
        ];
    }

    public function saveChecklistItem($user_id)
    {
        if (!$this->validate()) {
            return null;
        }
        $model = new ChecklistItems();
        $model->name = $this->name;
        $model->user_id = $user_id;
        $model->checklist_id = $this->checklist_id;
        $model->extra = 1;
       return $model->save();
    }
}