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
     * Подписаться\отписаться от пользователей. Модель сама проверит, подпсан уже пользователь, или нет.
     * Нельзя подписаться на самого себя.
     * @var int $friend_id id пользователя (POST)
     * @return array статус выполнения
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

    /**
     * Подтверждение, либо отклонение заявки в друзья
     * @var int $friend_id id пользователя (POST)
     * @var bool $status подтверждение\отклонение заявки (POST)
     * @return array статус выполнения
     */
    public function actionConfirmRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $friend_id = intval(Yii::$app->request->post('friendId'));
        $status = boolval(Yii::$app->request->post('status')) ;
        $user_id = Yii::$app->user->getId();
        $model = new Friends($user_id);
        if ($action = $model->confirmRequest($friend_id, $status)) {
            return [
                'status' => 'success',
                'action' => $action
            ];
        } else return [
            'status' => 'fail'
        ];
    }

    /**
     * @return string html список всех друзей пользователя
     */
    public function actionGetFriends()
    {
        $friends = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::FRIENDS);
        $incomingRequests = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::INCOMING_REQUESTS);
        $outgoingRequests = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::OUTGOING_REQUESTS);
        return $this->render('friendsPageView', [
            'friends' => $friends,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests,
        ]);
    }

    /**
     * Поиск друзей среди пользователей
     * @return string html список найденных пользователей
     */
    public function actionSearchFriends()
    {
        $searchModel = new SearchModel();
        if($searchModel->load(Yii::$app->request->post()) && $result = $searchModel->search()) {
            return $this->render('friendsSearch', [
                'searchModel' => $searchModel,
                'friends' => $result,
            ]);
        }
        return $this->render('friendsSearch', [
            'searchModel' => $searchModel
        ]);
    }
}