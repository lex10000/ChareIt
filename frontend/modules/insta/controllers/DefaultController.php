<?php
declare(strict_types=1);

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Post;
use frontend\modules\user\models\User;
use phpDocumentor\Reflection\Types\Mixed_;
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
     * @return string|null
     */
    public function actionCreate(): ?string
    {
        $model = new PostForm(Yii::$app->user->getId());

        if ($model->load(Yii::$app->request->post())) {
            $model->picture = UploadedFile::getInstance($model, 'picture');

            if ($post = $model->save()) {
                $posts[] = $post;
                return $this->renderPartial('instaPostsView', [
                    'posts' => $posts
                ]);
            } else return 'not save';
        } else return null;
    }

    /**
     * Удаляет пост. Если попытка удалить чужой пост, то ошибка доступа.
     * @return array статус выполнения, json формат
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = intval(Yii::$app->request->post('instaPostId'));

        $post = $this->findPost($id);

        if ($post->user_id === Yii::$app->user->getId()) {
            if (Yii::$app->storage->deleteFile($post->filename)
                && Yii::$app->storage->deleteFile('thumbnails/'.$post->filename)
                && $post->delete() ) {
                return ['status' => 'success'];
            } else {
                return [
                    'status' => 'fail',
                    'message' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается',
                ];
            }
        } else {
            return [
                'status' => 'access fail',
                'message' => 'Ошибка доступа',
            ];
        }
    }

//    public function actionIndex()
//    {
//        $postForm = new PostForm(Yii::$app->user->getId());
//        $friends = (new Friends(Yii::$app->user->getId()))->getAllFriends();
//    }
    /**
     * Возвращает посты. Если форма вызвана обычным запросом, то берутся первые n-записей,
     * если ajax`ом - то с номера переданной страницы
     * @param int|null $user_id если не передан, то вернуть все посты
     * @return string|Response|null html шаблон n-постов, или редирект, если пользователь не найден
     */
    public function actionGetFeed(int $user_id = null)
    {
        if ($user_id) {
            if (!User::findOne($user_id)) {
                return $this->redirect('/insta/get-feed');
            }
        }
        if (Yii::$app->request->isAjax) {
            $start_page = intval(Yii::$app->request->get('startPage'));
            $posts = (new Post())->getFeed($start_page, $user_id);
            if ($posts) {
                return $this->renderAjax('instaPostsView', [
                    'posts' => $posts
                ]);
            } else return null;
        };

        $posts = (new Post())->getFeed(0, $user_id);
        return $this->render('instaPostsView', [
            'posts' => $posts,
        ]);
    }

    /**
     * Возвращает новые посты, которые были сделаны после даты последнего поста
     * @return string|null html с новыми постами.
     */
    public function actionGetNewPosts(): ?string
    {
        $created_at = intval(Yii::$app->request->get('created_at')) ?? time();

        $posts = (new Post())->getNewPosts($created_at);

        if ($posts) {
            return $this->renderAjax('instaPostsView', [
                'posts' => $posts
            ]);
        } else return null;

    }

    /**
     * Лайк\дизлайк поста.
     * TODO: отрефакторить название метода, убрать возврат совершенного действия
     * @return array статус выполнения, если успех - то количество лайков, и совершенное действие (лайк\дизлайк)
     */
    public function actionLike(): array
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
     * @param int $post_id
     * @return \yii\db\ActiveRecord|null пост
     */
    private function findPost($post_id): ?\yii\db\ActiveRecord
    {
        $model = new Post();
        return $model->getPost($post_id);
    }

//    /**
//     * Получить топ самых популярных постов (по лайкам).
//     * @return string|null
//     */
//    public function actionGetTop()
//    {
//        $posts = (new Post())->getTopPosts();
//        return print_r($posts);
//        if ($posts) {
//            return $this->render('instaPostsView', [
//                'posts' => $posts
//            ]);
//        } else return null;
//    }
}
