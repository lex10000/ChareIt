<?php
namespace frontend\controllers;

use frontend\modules\user\models\LoginForm;
use yii\web\Controller;
use Yii;


class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Cтартовая страница
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->getId();
            return $this->redirect("/get-feed/$id");
        }
        $model = new LoginForm();
        return $this->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Страница "О"
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about', []);
    }
}
