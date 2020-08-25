<?php

namespace frontend\modules\checklist\models;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "checklist".
 *
 * @property int $id id чек-листа
 * @property string $name Название чек-листа
 * @property int $user_id id пользователя
 * @property int $status Статус
 * @property int $created_at Дата создания
 * @property int $updated_at Дата последнего редактирования
 */
class Checklist extends \yii\db\ActiveRecord
{

    const STATUS_DONE = 10;
    const STATUS_NEW = 1;
    const STATUS_PROCESS = 2;

    public static function tableName()
    {
        return 'checklist';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public function rules()
    {
        return [
            [['user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['status'], 'default', 'value' => self::STATUS_NEW],
            [['status'], 'in', 'range' => [self::STATUS_NEW, self::STATUS_DONE, self::STATUS_PROCESS]],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function findAllChecklists($user_id)
    {
        return self::find()
        ->where(['user_id' => $user_id])
        ->asArray()
        ->all();
    }

    public static function deleteChecklist($checklist_id, $user_id)
    {
        $checklist = self::findOne($checklist_id);
        if($checklist->user_id === $user_id) {
            return $checklist->delete();
        }
    }

    public static function deleteAllChecklists($user_id)
    {
        return self::deleteAll(['user_id' => $user_id]);
    }
}
