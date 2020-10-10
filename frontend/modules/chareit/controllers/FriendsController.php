<?php
declare(strict_types=1);

namespace frontend\modules\chareit\controllers;

use frontend\modules\chareit\models\Friends;
use frontend\modules\chareit\models\Post;
use frontend\modules\chareit\models\PostLikes;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use frontend\modules\chareit\models\forms\SearchModel;

class FriendsController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome()->send();
        }
        return parent::beforeAction($action);
    }

    /**
     * Подписаться\отписаться от пользователей. Модель сама проверит, подпсан уже пользователь, или нет.
     * Нельзя подписаться на самого себя.
     * @return array статус выполнения
     * @var int $friend_id id пользователя (POST)
     */
    public function actionChangeSubscribeStatus(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $friend_id = intval(Yii::$app->request->post('friendId'));
        $user_id = Yii::$app->user->getId();

        if ($friend_id === $user_id) {
            return [
                'status' => 'fail',
                'action' => 'self-subscribe'
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
     * @return array статус выполнения
     * @var bool $status подтверждение\отклонение заявки (POST)
     * @var int $friend_id id пользователя (POST)
     */
    public function actionConfirmRequest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $friend_id = intval(Yii::$app->request->post('friendId'));
        $status = boolval(Yii::$app->request->post('status'));
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
        $searchModel = new SearchModel();
        $friends = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::FRIENDS);
        $incomingRequests = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::INCOMING_REQUESTS);
        $outgoingRequests = (new Friends(Yii::$app->user->getId()))->getFriends(Friends::OUTGOING_REQUESTS);
        return $this->render('friendsPageView', [
            'friends' => $friends,
            'incomingRequests' => $incomingRequests,
            'outgoingRequests' => $outgoingRequests,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Поиск друзей среди пользователей
     * @return string html список найденных пользователей
     */
    public function actionSearchFriends()
    {
        $searchModel = new SearchModel();
        if ($searchModel->load(Yii::$app->request->post())) {
            if ($result = $searchModel->search()) {
                return $this->renderAjax('friendsSearch', [
                    'users' => $result,
                ]);
            } else return 'Результатов нет';
        }
    }

    public function actionLikedUsers($post_id)
    {
        if(!Post::findOne(intval($post_id))) {
            throw new NotFoundHttpException('Поста с данным id не существует.');
        } else {
            $users = PostLikes::getLikedUsers(intval($post_id), false, false);
            return $this->render('friendsList', [
                'friends' => $users,
            ]);
        }
    }
}