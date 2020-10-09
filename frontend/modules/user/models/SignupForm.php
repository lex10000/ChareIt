<?php
namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use frontend\modules\user\models\User;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $password;
    public $password_repeat;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            //['username', 'unique', 'targetClass' => '\frontend\modules\User\models\User', 'message' => 'Данный логин уже занят'],
            ['username', 'string', 'min' => 3, 'max' => 55],

            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],

            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'message' => "Пароли не совпадают"],

        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'логин',
            'password' => 'пароль',
            'password_repeat' => 'пароль',
        ];
    }

    /**
     * Signs user up.
     *
     * @return bool whether the creating new account was successful and email was sent
     */
    public function signup()
    {
        if (!$this->validate()) {
            return false;
        }
        $user = new User();
        $user->email = time();
        $user->username = $this->username;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generateEmailVerificationToken();
        $user->generatePasswordResetToken();
        return $user->save();

    }

    /**
     * Sends confirmation email to user
     * @param User $user user model to with email should be send
     * @return bool whether the email was sent
     */
    protected function sendEmail($user)
    {
        return Yii::$app
            ->mailer
            ->compose(
                ['html' => 'emailVerify-html', 'text' => 'emailVerify-text'],
                ['user' => $user]
            )
            ->setFrom(['lex10000@yandex.ru' => 'alex'])
            ->setTo($this->email)
            ->setReplyTo($this->email)
            ->setSubject('Account registration at ' . Yii::$app->name)
            ->send();
    }
}
