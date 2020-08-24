<?php
namespace frontend\controllers;

use frontend\models\Post;
use frontend\modules\user\models\LoginForm;
use yii\web\Controller;
use frontend\models\User;
use yii\web\Response;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->getId();
            return $this->redirect("/profile/$id");
        }
        $model = new LoginForm();
        return $this->render('welcome', [
            'model' => $model
        ]);
    }
}
