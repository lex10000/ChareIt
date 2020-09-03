<?php

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Post;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\modules\insta\models\forms\PostForm;
use Yii;

/**
 * Default controller for the `post` module
 */
class DefaultController extends Controller
{

    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/index');
        }
        $model = new PostForm(Yii::$app->user->identity->getId());

        if ($model->load(Yii::$app->request->post())) {
            $model->picture = UploadedFile::getInstance($model, 'picture');
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Фотография добавлена!');
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionFeed()
    {
        if(Yii::$app->request->isAjax) {
            $start_page = Yii::$app->request->get('startPage');
            $posts = (new Post())->getFeed($start_page);
            if($posts) {
                return $this->renderPartial('feedView', [
                    'posts' => $posts
                ]);
            } else return false;
        };

        $posts = (new Post())->getFeed();
        return $this->render('feedView', [
            'posts' => $posts
        ]);
    }
//    public function actionLike()
//    {
//        if (Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        $post_id = Yii::$app->request->post('id');
//        $action = Yii::$app->request->post('action');
//        $post = $this->findPost($post_id);
//
//        $user = Yii::$app->user->identity;
//        if($action==='like') {
//            $post->like($user);
//        } elseif($action==='dislike') {
//            $post->removeLike($user);
//        }
//        return [
//            'success' => true,
//            'countLikes' => $post->countLikes(),
//        ];
//    }
//
//    public function findPost($post_id)
//    {
//        $model = new Post();
//        return $model->getPost($post_id);
//    }

//    public function actionGetusers()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $limit = 50;
//        $page = Yii::$app->request->post('page') ?? 1;
//        $posts = new Post();
//        $data = $posts->getAllPosts(Yii::$app->user->identity->getId(), $page, $limit );
//        return [
//            'success' => true,
//            'data' => $data,
//        ];
//    }
}
