<?php
declare(strict_types=1);

namespace frontend\modules\chareit\controllers;

use frontend\modules\user\models\forms\ChangePasswordForm;
use frontend\modules\user\models\forms\ProfileForm;
use frontend\modules\user\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use Yii;

/**
 * Основной контроллер ChareIt.
 */
class DefaultController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome()->send();
        }
        return parent::beforeAction($action);
    }

    /**
     * Лента
     * @return string
     */
    public function actionFeed()
    {
        $this->view->title = 'Лентач';
        return $this->renderContent('');
    }

    /**
     * Страница профиля
     * @param $user_id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionProfile($user_id)
    {
        $user = User::findById(intval($user_id));
        if($user) {
            return $this->render('profileView', [
                'user' => $user,
            ]);
        } else throw new NotFoundHttpException('Данный пользователь не найден');
    }

    /**
     * Страница топ-постов.
     * @return string
     */
    public function actionTop(): string
    {
        $this->view->title = 'Топ постов';
        return $this->renderContent('');
    }

    /**
     * Страница настроек.
     * @return string
     */
    public function actionSettings()
    {
        $changePasswordModel = new ChangePasswordForm();
        if ($changePasswordModel->load(Yii::$app->request->post()) && $changePasswordModel->changePassword()) {
            Yii::$app->session->setFlash('changePassword', 'Пароль успешно изменен!');
        }

        $user = new ProfileForm();
        $user->about = Yii::$app->user->identity->about;
        if ($user->load(Yii::$app->request->post())) {
            $user->picture = UploadedFile::getInstance($user, 'picture');
            if ($user->save()) {
                Yii::$app->session->setFlash('changeSettings', 'Данные сохранены!');
            }
        }
        return $this->render('profile_settings_views/settingsView', [
            'changePasswordModel' => $changePasswordModel,
            'user' => $user
        ]);
    }
}