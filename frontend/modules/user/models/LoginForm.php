<?php
namespace frontend\modules\user\models;

use Yii;
use yii\base\Model;
use frontend\modules\user\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    private $_user;

    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['username', 'string', 'min' => 3, 'tooShort' => 'Имя пользователя содержит более 3х символов'],
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'логин',
            'password' => 'пароль',
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Неверное имя пользователя или пароль');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser());
        }
        
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
