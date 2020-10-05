<?php
declare(strict_types=1);

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Friends;
use frontend\modules\insta\models\Post;
use frontend\modules\user\models\forms\ChangePasswordForm;
use frontend\modules\user\models\forms\ProfileForm;
use frontend\modules\user\models\User;
use frontend\modules\insta\models\PostLikes;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use frontend\modules\insta\models\forms\PostForm;
use Yii;

/**
 * Основной контроллер ChareIt.
 */
class DefaultController extends Controller
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome()->send();
        }
        return parent::beforeAction($action);
    }

    /**
     * Создание поста.
     * @return string|null
     */
    public function actionCreate()
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
     * @return array статус выполнения, json формат
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     * @var int $id id поста (POST)
     */
    public function actionDelete(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = intval(Yii::$app->request->post('instaPostId'));
        $post = $this->findPost($id);
        if ($post->user_id === Yii::$app->user->getId()) {
            if (Yii::$app->storage->deleteFile('uploads/' . $post->filename)
                && Yii::$app->storage->deleteFile('uploads/thumbnails/' . $post->filename)
                && $post->delete()) {
                return ['status' => 'success'];
            } else return [
                'status' => 'fail',
                'message' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается',
            ];
        } else return [
            'status' => 'access fail',
            'message' => 'Ошибка доступа',
        ];
    }

    /**
     * Возвращает ленту.
     * @return string
     */
    public function actionGetFeed(): ?string
    {
        if (Yii::$app->request->isAjax) {
            $start_page = intval(Yii::$app->request->get('startPage'));
            $posts = (new Post())->getFeed($start_page);
            if ($posts) {
                return $this->renderAjax('postsView', [
                    'posts' => $posts
                ]);
            } else return null;
        }
        $posts = (new Post())->getFeed();
        return $this->render('postsView', [
            'posts' => $posts
        ]);
    }

    public function actionProfile(int $user_id)
    {
        $user = User::findById(intval($user_id));
        if (!$user) {
            throw new NotFoundHttpException('Данный пользователь не найден!');
        }
        if ($user && Friends::isUserIn($user_id, Friends::FRIENDS) || $user_id == Yii::$app->user->getId()) {
            $posts = (new Post())->getFeed(0, intval($user_id));
            $posts = $posts ?: 'empty';
        } else {
            Yii::$app->session->setFlash('access-denied', 'Вы не можете просматривать посты данного пользователя. Добавьте его в друзья!');
            $posts = null;
        }
        return $this->render('profileView', [
            'user' => $user,
            'posts' => $posts
        ]);
    }

//    public function actionCheckNewPosts()
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        return [
//            'status' => true
//        ];
//    }

    /**
     * Лайк\дизлайк поста.
     * @return array статус выполнения, если успех - то количество лайков, и совершенное действие (лайк\дизлайк)
     * @var string $action тип действия (лайк\дизлайк)
     * @var int $post_id id поста
     */
    public function actionLike(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post_id = intval(Yii::$app->request->post('postId'));
        $action = (new PostLikes())->changeStatus(Yii::$app->user->getId(), $post_id);
        switch ($action) {
            case PostLikes::STATUS_EXCEEDED :
            {
                return [
                    'status' => PostLikes::STATUS_EXCEEDED,
                    'message' => 'Лимит лайков на сегодня уже превышен, завтра вы сможете продолжить'
                ];
            }
            case ('sadd' || 'srem'):
            {
                return [
                    'status' => 'success',
                    'countLikes' => PostLikes::countLikes($post_id),
                    'action' => $action,
                ];
            }
            default:
            {
                return [
                    'status' => 'fail',
                    'message' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается'
                ];
            }
        }
    }

    /**
     * Получить пост по его id
     * @param int $post_id id поста
     * @return \yii\db\ActiveRecord|null пост
     */
    private function findPost($post_id): ?\yii\db\ActiveRecord
    {
        return (new Post())->getPost($post_id);
    }

    /**
     * Получить топ самых популярных постов.
     * @return string
     */
    public function actionGetTop(): string
    {
        $posts = (new Post())->getTopPosts();
        return $this->render('postsView', [
            'posts' => $posts
        ]);
    }

    /**
     * Страница настроек.
     * @return string
     */
    public function actionSettings()
    {
        $changePasswordModel = new ChangePasswordForm();
        if ($changePasswordModel->load(Yii::$app->request->post()) && $changePasswordModel->changePassword()) {
            Yii::$app->session->setFlash('changePassword', 'Пароль успешно изменен!');
        }

        $user = new ProfileForm();
        $user->about = Yii::$app->user->identity->about;
        if ($user->load(Yii::$app->request->post())) {
            $user->picture = UploadedFile::getInstance($user, 'picture');
            if ($user->save()) {
                Yii::$app->session->setFlash('changeSettings', 'Данные сохранены!');
            }
        }
        return $this->render('profile_settings_views/settingsView', [
            'changePasswordModel' => $changePasswordModel,
            'user' => $user
        ]);
    }
}