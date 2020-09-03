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

    public function actionGetFeed()
    {
        $user_id = Yii::$app->request->get('all') ? Yii::$app->user->getId() : null;

        if (Yii::$app->request->isAjax) {
            $start_page = Yii::$app->request->get('startPage');
            $posts = (new Post())->getFeed($start_page, $user_id);
            if ($posts) {
                return $this->renderPartial('instaPostsView', [
                    'posts' => $posts
                ]);
            } else return false;
        };

        $posts = (new Post())->getFeed(1, $user_id);
        return $this->render('instaPostsView', [
            'posts' => $posts
        ]);
    }

    public function actionLike()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        Yii::$app->response->format = Response::FORMAT_JSON;

        $insta_post_id = Yii::$app->request->post('instaPostId');
        $post = $this->findPost($insta_post_id);

        $user = Yii::$app->user->identity;
        if ($post->like($user)) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
            ];
        }
    }


    public function findPost($post_id)
    {
        $model = new Post();
        return $model->getPost($post_id);
    }

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
