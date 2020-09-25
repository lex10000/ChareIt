<?php
declare(strict_types = 1);

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Friends;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use frontend\modules\insta\models\forms\SearchModel;

class FriendsController extends Controller
{
    /**
     * @return array
     */
    public function actionChangeSubscribeStatus() : array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $friend_id = intval(Yii::$app->request->post('friendId'));
        $user_id = Yii::$app->user->getId();

        if($friend_id === $user_id) {
            return [
                'status' => 'fail',
                'action' => 'Нельзя подписаться на самого себя'
            ];
        }
        $model = new Friends($user_id);
        if ($action = $model->changeSubscribeStatus($friend_id)) {
            return [
                'status' => 'success',
                'action' => $action
            ];
        } else return [
            'status' => 'fail'
        ];
    }

    public function actionGetFriends()
    {
        $friends = (new Friends(Yii::$app->user->getId()))->getAllFriends();
        return $this->render('friendsView', [
            'friends' => $friends
        ]);
    }

    /**
     * Поиск друзей среди пользователей
     * @return string
     */
    public function actionSearchFriends()
    {
        $searchModel = new SearchModel();
        if($searchModel->load(Yii::$app->request->post()) && $result = $searchModel->search()) {
            return $this->render('friendsSearch', [
                'searchModel' => $searchModel,
                'friends' => $result
            ]);
        }
        return $this->render('friendsSearch', [
            'searchModel' => $searchModel
        ]);
    }
}