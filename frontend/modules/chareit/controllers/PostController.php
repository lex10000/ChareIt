<?php


namespace frontend\modules\chareit\controllers;

use frontend\modules\chareit\models\Comment;
use frontend\modules\chareit\models\forms\PostForm;
use frontend\modules\chareit\models\Post;
use frontend\modules\chareit\models\PostLikes;
use frontend\modules\user\models\User;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\UploadedFile;

class PostController extends \yii\web\Controller
{
    /**
     * Создание поста.
     * @return string|null
     */
    public function actionCreatePost()
    {
        $model = new PostForm(Yii::$app->user->getId());
        if ($model->load(Yii::$app->request->post())) {
            $model->picture = UploadedFile::getInstance($model, 'picture');
            if ($post = $model->save()) {
                return $this->redirect(Url::to("/profile/" . Yii::$app->user->getId()));
            }
        }
    }

    /**
     * Удаляет пост. Если попытка удалить чужой пост, то ошибка доступа.
     * @return Response статус выполнения, json формат
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @var int $id id поста (POST)
     */
    public function actionDeletePost(): Response
    {
        $id = intval(Yii::$app->request->post('postId'));
        $post = (new Post())->getPost($id);
        if ($post->user_id === Yii::$app->user->getId()) {
            if (Yii::$app->storage->deleteFile('uploads/' . $post->filename)
                && Yii::$app->storage->deleteFile('uploads/thumbnails/' . $post->filename)
                && $post->delete()) {
                return $this->asJson(['status' => 'success']);
            } else return $this->asJson([
                'status' => 'fail',
                'message' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается',
            ]);
        } else return $this->asJson([
            'status' => 'access fail',
            'message' => 'Ошибка доступа',
        ]);
    }

    /**
     * Возвращает ленту.
     * @param int $startPage
     * @return Response
     */
    public function actionGetFeed($startPage = 0): ?Response
    {
        $posts = (new Post())->getFeed(intval($startPage));
        return $this->sendFeed($posts);
    }

    public function actionGetProfile($user_id, $startPage = 0)
    {
        $user = User::findById(intval($user_id));
        if(!$user) {
            return $this->asJson([
                'status' => 'user not found'
            ]);
        }
        $posts = (new Post())->getProfileFeed(intval($user_id), intval($startPage));
        return $this->sendFeed($posts);

    }
    public function actionGetTop()
    {
        $posts = (new Post())->getTopFeed();
        return $this->sendFeed($posts);
    }

    private function prepareFeed($posts)
    {
        foreach ($posts as &$post) {
            $post_id = intval($post['id']);
            $user_id = intval($post['user_id']);
            $post['isLikedByUser'] = PostLikes::isChangedByUser($user_id, $post_id);
            $post['isOwner'] = (Yii::$app->user->getId() === $user_id);
            $post['likedUsers'] = PostLikes::getLikedUsers($post_id);
            //$post['countComments'] = Comment::countComments($post_id);
        }
        unset($post);
        return $posts;
    }

    /**
     * Лайк\анлайк поста.
     * @return Response статус выполнения, если успех - то количество лайков, и совершенное действие
     * @var string $action тип действия (лайк\анлайк)
     * @var int $post_id id поста
     */
    public function actionLikePost(): Response
    {
        $post_id = intval(Yii::$app->request->post('postId'));
        $action = (new PostLikes())->changeStatus(Yii::$app->user->getId(), $post_id);
        switch ($action) {
            case PostLikes::STATUS_EXCEEDED :
            {
                return $this->asJson([
                    'status' => PostLikes::STATUS_EXCEEDED,
                    'message' => 'Лимит лайков на сегодня уже превышен, завтра вы сможете продолжить'
                ]);
            }
            case ('sadd' || 'srem'):
            {
                return $this->asJson([
                    'status' => 'success',
                    'likedUsers' => PostLikes::getLikedUsers($post_id),
                ]);
            }
            default:
            {
                return $this->asJson([
                    'status' => 'fail',
                    'message' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается'
                ]);
            }
        }
    }

    public function actionGetLikedUsers()
    {
        $post_id = intval(Yii::$app->request->post('postId'));
        $users = PostLikes::getLikedUsers($post_id);
        return $this->asJson([
            'status' => 'success',
            'likedUsers' => $users
        ]);
    }

    private function sendFeed($posts)
    {
        if ($posts) {
            return $this->asJson([
                'status' => 'success',
                'posts' => $this->prepareFeed($posts)
            ]);
        } else {
            return $this->asJson([
                'status' => 'empty'
            ]);
        }
    }
}