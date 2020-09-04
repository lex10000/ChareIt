<?php

namespace frontend\modules\user\controllers;

use frontend\modules\user\models\LoginForm;
use frontend\modules\user\components\AuthHandler;
use frontend\modules\user\models\PasswordResetRequestForm;
use frontend\modules\user\models\ResendVerificationEmailForm;
use frontend\modules\user\models\ResetPasswordForm;
use frontend\modules\user\models\SignupForm;
use frontend\modules\user\models\VerifyEmailForm;
use yii\base\InvalidArgumentException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use Yii;
use frontend\models\User;
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
                Yii::$app->session->setFlash('success', 'Thank you for registration.');

                $identity = User::findOne(['username' => $model->username]);
                Yii::$app->user->login($identity);

                $id = $identity->getId();
                return $this->redirect("/profile/$id");

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
            return $this->redirect("/profile/$id");

        } else {
            Yii::$app->session->setFlash('danger', 'Неверное имя пользователя или пароль.');
            return $this->goBack();
        }
    }

    /**
     * Requests password reset.
     *
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
     * Verify email address
     *
     * @param string $token
     * @return yii\web\Response
     * @throws BadRequestHttpException
     */
//    public function actionVerifyEmail($token)
//    {
//        try {
//            $model = new VerifyEmailForm($token);
//        } catch (InvalidArgumentException $e) {
//            throw new BadRequestHttpException($e->getMessage());
//        }
//        if ($user = $model->verifyEmail()) {
//            if (Yii::$app->user->login($user)) {
//                Yii::$app->session->setFlash('success', 'Your email has been confirmed!');
//                return $this->goHome();
//            }
//        }
//
//        Yii::$app->session->setFlash('error', 'Sorry, we are unable to verify your account with provided token.');
//        return $this->goHome();
//    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
//    public function actionResendVerificationEmail()
//    {
//        $model = new ResendVerificationEmailForm();
//        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
//            if ($model->sendEmail()) {
//                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');
//                return $this->goHome();
//            }
//            Yii::$app->session->setFlash('error', 'Sorry, we are unable to resend verification email for the provided email address.');
//        }
//
//        return $this->render('resendVerificationEmail', [
//            'model' => $model
//        ]);
//    }


    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
