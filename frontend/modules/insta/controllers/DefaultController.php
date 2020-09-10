<?php
declare(strict_types=1);

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
     * @return string
     */
    public function actionCreate()
    {
        $model = new PostForm(Yii::$app->user->getId());

        if ($model->load(Yii::$app->request->post())) {
            $model->picture = UploadedFile::getInstance($model, 'picture');

            if ($id = $model->save()) {
                $posts = (new Post())->find()->where(['id' => $id])->limit(1)->asArray()->all();

                return $this->renderPartial('instaPostsView', [
                    'posts' => $posts
                ]);
            }
        }
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

        if ($post->user_id === Yii::$app->user->getId()) {
            if (Yii::$app->storage->deleteFile($post->filename) && $post->delete()) {
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
     * @param int|null $user_id если не передан, то вернуть все посты
     * @return string html шаблон n-постов
     */
    public function actionGetFeed($user_id = null)
    {
        if (Yii::$app->request->isAjax) {
            $start_page = intval(Yii::$app->request->get('startPage'));
            $posts = (new Post())->getFeed($start_page, $user_id);
            if ($posts) {
                return $this->renderAjax('instaPostsView', [
                    'posts' => $posts
                ]);
            } else return false;
        };

        $posts = (new Post())->getFeed(0, $user_id);
        return $this->render('instaPostsView', [
            'posts' => $posts,
        ]);
    }

    public function actionGetNewPosts()
    {
        $created_at = intval(Yii::$app->request->get('created_at')) ?? time();

        $posts = (new Post())->getNewPosts($created_at);

        if ($posts) {
            return $this->renderAjax('instaPostsView', [
                'posts' => $posts
            ]);
        } else return false;

    }

    /**
     * @return array|string[]
     */
    public function actionLike()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $insta_post_id = intval(Yii::$app->request->post('instaPostId'));
        $action = Yii::$app->request->post('action');

        if ($action = Post::changeStatus(Yii::$app->user->getId(), $insta_post_id, $action)) {
            $count = Post::countLikes($insta_post_id);
            return [
                'status' => 'success',
                'countLikes' => $count,
                'action' => $action,
            ];
        } else {
            return [
                'status' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается',
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
}
