<?php
declare(strict_types=1);

namespace frontend\modules\insta\controllers;

use frontend\modules\insta\models\Friends;
use frontend\modules\insta\models\Post;
use frontend\modules\user\models\forms\ChangePasswordForm;
use frontend\modules\user\models\forms\ProfileForm;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Controller;
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
       if(Yii::$app->user->isGuest) {return $this->goHome()->send();}
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
                $post->addToTop();
                return $this->redirect(Url::to("/get-feed/".Yii::$app->user->getId()));
            } else return 'not save';
        } else return null;
    }

    /**
     * Удаляет пост. Если попытка удалить чужой пост, то ошибка доступа.
     * @var int $id id поста (POST)
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
            if (Yii::$app->storage->deleteFile('uploads/'.$post->filename)
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
     * Возвращает посты. Если форма вызвана обычным запросом, то берутся первые n-записей,
     * если ajax`ом - то с номера переданной страницы.
     * @param int|null $user_id если не передан, то вернуть посты свои и друзей
     * @return string|Response|null html шаблон n-постов, или редирект, если пользователь не найден,или пользователь
     * не является другом.
     */
    public function actionGetFeed(int $user_id = null)
    {
        if ($user_id) {
            $user = (new Query())
                ->select(['id', 'about', 'picture', 'username'])
                ->from('user')
                ->where('id=:id')
                ->addParams([':id' => $user_id])
                ->limit(1)
                ->one();
            if (!$user || !((Friends::isUserIn($user_id, Friends::FRIENDS) || $user_id == Yii::$app->user->getId()))) {
                return $this->redirect('/get-feed');
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
        if($user_id) {
            $user_info[] = $user;
            return $this->render('instaPostsView', [
                'posts' => $posts,
                'isRenderUserInfo' => true,
                'user' => $user_info
            ]);
        } else return $this->render('instaPostsView', ['posts' => $posts]);
    }

    /**
     * Возвращает новые посты, которые были сделаны после даты последнего поста
     * @var int $created_at время создание последнего полученного поста
     * @return string|null html с новыми постами.
     */
    public function actionGetNewPosts(): ?string
    {
        $created_at = intval(Yii::$app->request->get('created_at')) ?? time();
        $posts = (new Post())->getNewPosts($created_at);
        if ($posts) {
            return $this->renderAjax('instaPostsView', ['posts' => $posts]);
        } else return null;
    }

    /**
     * Лайк\дизлайк поста.
     * @var int $post_id id поста
     * @var string $action тип действия (лайк\дизлайк)
     * @return array статус выполнения, если успех - то количество лайков, и совершенное действие (лайк\дизлайк)
     */
    public function actionLike(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post_id = intval(Yii::$app->request->post('instaPostId'));
        $action = Yii::$app->request->post('action');
        if ($action = (new Post())->changeStatus(Yii::$app->user->getId(), $post_id, $action)) {
            return [
                'status' => 'success',
                'countLikes' => Post::countLikes($post_id),
                'action' => $action,
            ];
        } else return ['status' => 'Упс, что-то пошло не так, команда лучших разработчиков уже разбирается'];
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
    public function actionGetTop() : string
    {
        $posts = (new Post())->getTopPosts();
        return $this->render('instaPostsView', [
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
        if($changePasswordModel->load(Yii::$app->request->post()) && $changePasswordModel->changePassword())
        {
            Yii::$app->session->setFlash('changePassword', 'Пароль успешно изменен!');
        }

        $user = new ProfileForm();
        $user->about = Yii::$app->user->identity->about;
        if($user->load(Yii::$app->request->post())) {
            $user->picture = UploadedFile::getInstance($user, 'picture');
            if($user->save()) {
                Yii::$app->session->setFlash('changeSettings', 'Данные сохранены!');
            }
        }
        return $this->render('profile_settings_views/settingsView', [
            'changePasswordModel' => $changePasswordModel,
            'user' => $user
        ]);
    }
}