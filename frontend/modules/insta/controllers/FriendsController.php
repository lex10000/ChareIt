<?php
declare(strict_types = 1);

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Friends;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class FriendsController extends Controller
{
    /**
     * @return array
     */
    public function changeSubscribeStatus() : array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $friend_id = Yii::$app->request->post('friendId');
        $user_id = Yii::$app->user->getId();
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
}