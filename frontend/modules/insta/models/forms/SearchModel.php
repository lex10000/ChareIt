<?php

namespace frontend\modules\insta\models\forms;

use frontend\modules\user\models\User;
use yii\base\Model;
use yii\db\Expression;
use yii\db\Query;

class SearchModel extends Model
{
    public $userQuery;

    public function rules()
    {
        return [
            ['userQuery', 'trim'],
            ['userQuery','string', 'min' => 3, 'max' => 50]
        ];
    }

    public function attributeLabels()
    {
        return [
            'userQuery' => 'Имя пользователя',
        ];
    }
    public function search()
    {
        return (new Query())
            ->select(['id', 'username', 'picture', 'about'])
            ->from('user')
            ->where(new Expression('username LIKE :param', [':param' => "%$this->userQuery%"]))
            ->andWhere('status=10')
            ->all();
    }
}
