<?php
namespace frontend\modules\user\models\forms;

use Yii;
use yii\base\InvalidArgumentException;
use yii\base\Model;
use frontend\modules\user\models\User;

/**
 * Password change form
 */
class ChangePasswordForm extends Model
{
    public $password;
    public $password_repeat;

    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            ['password_repeat', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' =>'пароль',
            'password_repeat' => 'повторите пароль'
        ];
    }

    /**
     * Изменение пароля со страницы настроек.
     *
     * @return bool if password was changed.
     */
    public function changePassword()
    {
        if($this->validate()) {
            $user = User::findOne(Yii::$app->user->getId());
            $user->setPassword($this->password);

            return $user->save(false);
        }
    }
}
