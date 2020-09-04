<?php

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Post;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\modules\insta\models\forms\PostForm;
use Yii;

/**
 * Контроллер инсты. CRUD, а так же лайк\дизлайк постов.
 * Модели: Post, PostForm
 */
class DefaultController extends Controller
{

    /**
     * Создание поста.
     * TODO: переделать под ajax
     * @return string форма для создания поста, либо сообщение в случае создания
     */
    public function actionCreate()
    {
        $model = new PostForm(Yii::$app->user->getId());

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

    /**
     * Удаляет пост. Если попытка удалить чужой пост, то ошибка доступа.
     * @return string[] статус выполнения, json формат
     */
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = intval(Yii::$app->request->post('instaPostId'));

        $post = $this->findPost($id);

        if($post->user_id === Yii::$app->user->getId()) {
            if(Yii::$app->storage->deleteFile($post->filename) && $post->delete()) {
                return ['status' => 'success'];
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Ошибка или файл не найден',
                ];
            }
        } else {
            return [
                'status' => 'access fail',
                'message' => 'Ошибка доступа',
            ];
        }
    }

    /**
     * Возвращает посты. Если форма вызвана обычным запросом, то берутся первые n-записей, если ajax`ом - то с номера переданной страницы
     * @param int|nul $user_id если не передан, то вернуть все посты
     * @return string html шаблон n-постов
     */
    public function actionGetFeed($user_id = null)
    {
        if (Yii::$app->request->isAjax) {
            $start_page = Yii::$app->request->get('startPage');
            $posts = (new Post())->getFeed($start_page, $user_id);
            if ($posts) {
                return $this->renderPartial('instaPostsView', [
                    'posts' => $posts
                ]);
            } else return false;
        };

        $posts = (new Post())->getFeed(0, $user_id);

        return $this->render('instaPostsView', [
            'posts' => $posts,
        ]);
    }

    /**@deprecated
     * Лайк поста. Еще недоделанный.
     * @return bool[]|Response
     */
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

    /**
     * Получить пост по его id
     * @param $post_id
     * @return array|\yii\db\ActiveRecord|null пост
     */
    private function findPost($post_id)
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
