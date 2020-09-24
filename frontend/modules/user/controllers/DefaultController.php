<?php

namespace frontend\modules\user\controllers;

use frontend\modules\user\models\forms\ChangePasswordForm;
use frontend\modules\user\models\LoginForm;
use frontend\modules\user\components\AuthHandler;
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
use yii\web\UploadedFile;
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

    public function actionMenu()
    {
        return $this->render('index', [
            'user_id' => Yii::$app->user->getId(),
        ]);
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
        }
        else {
            if ($model->load(Yii::$app->request->post()) && $model->signup()) {
                $identity = User::findOne(['username' => $model->username]);
                Yii::$app->user->login($identity);
                return $this->redirect("/insta/get-feed");

            } else {
                Yii::$app->session->setFlash('danger', 'Ups, что-то пошло не так. Попробуйте еще раз.');
                return $this->goBack();
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
            $id = Yii::$app->user->getId();
            return $this->redirect("/insta/get-feed");
        } else {
            Yii::$app->session->setFlash('danger', 'Неверное имя пользователя или пароль.');
            return $this->goBack();
        }
    }

    /**
     * Requests password reset.
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
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
     * Страница настроек.
     * TODO: удаление аккаунта
     * @return string
     */
    public function actionSettings()
    {
        $this->layout = '@frontend/modules/insta/views/layouts/instaLayout';
        $user = new ProfileForm();
        $changePasswordModel = new ChangePasswordForm();
        $user->about = Yii::$app->user->identity->about;
        if($changePasswordModel->load(Yii::$app->request->post()) && $changePasswordModel->changePassword())
        {
            Yii::$app->session->setFlash('changePassword', 'Пароль успешно изменен!');
        }
        if($user->load(Yii::$app->request->post())) {
            $user->picture = UploadedFile::getInstance($user, 'picture');
            if($user->save()) {
                Yii::$app->session->setFlash('changeSettings', 'Данные сохранены!');
            }
        }
        return $this->render('profile_settings_views/settingsView', [
            'changePasswordModel' => $changePasswordModel,
            'user' => $user
        ]);
    }

    /**
     * Удаление аккаунта
     */
    public function actionDeleteUser()
    {
        if(User::deleteUser()) {
            $this->actionLogout();
        }
    }
}
