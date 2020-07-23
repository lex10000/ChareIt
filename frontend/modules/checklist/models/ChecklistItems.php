<?php

namespace frontend\modules\checklist\models;

use Yii;

/**
 * This is the model class for table "checklist_items".
 *
 * @property int $id id пункта
 * @property int|null $checklist_id id чек-листа
 * @property string|null $name название пункта чек-листа
 * @property int|null $extra Обязательный/необязательный
 */
class ChecklistItems extends \yii\db\ActiveRecord
{

    const EXTRA_REQUIRED = 1;
    const EXTRA_NOT_REQUIRED = 0;


    public static function tableName()
    {
        return 'checklist_items';
    }

    public static function deleteChecklistItem($id)
    {
        return self::findOne($id)->delete();
    }

    public function rules()
    {
        return [
            [['extra', 'checklist_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            ['extra', 'default', 'value' => self::EXTRA_REQUIRED],
            ['extra', 'in', 'range' => [self::EXTRA_REQUIRED, self::EXTRA_NOT_REQUIRED]],
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'checklist_id' => 'Checklist ID',
            'name' => 'Name',
            'extra' => 'Extra',
        ];
    }

    public static function getChecklistItems($checklist_id)
    {
        return self::find()
            ->where(['checklist_id' => $checklist_id])
            ->asArray()
            ->all();
    }
}
