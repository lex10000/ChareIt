<?php

namespace frontend\modules\user\controllers;

use frontend\modules\user\models\forms\ChangePasswordForm;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResendVerificationEmailForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use frontend\modules\user\models\User;
use frontend\modules\user\models\VerifyEmailForm;
use frontend\modules\user\models\forms\ProfileForm;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use Yii;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Default controller for the `user` module
 */
class DefaultController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignUp()
    {
        $model = new SignupForm();
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            return $this->renderAjax('signup', [
                'model' => $model,
            ]);
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->signup()) {
                Yii::$app->session->setFlash('signup-success', 'Для завершения регистрации перейдите по ссылке, 
                указанной в письме, которое было отправлено на ваш почтовый ящик.');
                return $this->render('//site/signup-result');
            } else {
                Yii::$app->session->setFlash('signup-fail', 'Ups, что-то пошло не так. Попробуйте еще раз.');
                return $this->render('//site/signup-result');
            }
        }
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect("/get-feed");
        } else {
            Yii::$app->session->setFlash('invalid_login', 'Неверное имя пользователя или пароль.');
            return $this->goBack();
        }
    }

    /**
     * Logs out the current user.
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * Удаление аккаунта
     */
    public function actionDeleteUser()
    {
        if (User::deleteUser()) {
            $this->actionLogout();
        }
    }

    /**
     * Подтверждение регистрации по емайл
     * @param string $token
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionVerifyEmail(string $token)
    {
        if ($user = ((new VerifyEmailForm($token))->verifyEmail())) {
            Yii::$app->user->login($user);
            return $this->redirect("/get-feed");
        } else throw new BadRequestHttpException('ошибка');
    }
}
