<?php
declare(strict_types=1);

namespace frontend\modules\insta\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $filename
 * @property string|null $description
 * @property int $created_at
 * @property string $thumbnail
 */
class Post extends ActiveRecord
{
    private $likesLimit = 5;

    /**
     * @type int кол-во получение постов за один запрос
     */
    private $limit = 5;

    /**
     * @type int кол-во постов в топ-листе
     */
    private $top = 10;

    /**
     * Удаление поста. При удалении - удалить все данные о посте из редиса.
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function delete()
    {
        Yii::$app->redis->srem("post:{$this->id}:likes", $this->user_id);
        Yii::$app->redis->zrem('topz', $this->id);
        return parent::delete();
    }

    /**
     * Возвращает массив с инста-постами
     * @param int $start_page стартовый номер поиска постов (для асинхронной загрузки)
     * @param int|null $user_id если не указан, то вернуть посты свои и всех друзей
     * @return array|null массив с постами
     */
    public function getFeed(int $start_page = 0, int $user_id = null): ?array
    {
        if($user_id) {
            $condition = $user_id;
        } else {
            $condition = Yii::$app->redis->smembers(Yii::$app->user->getId().Friends::FRIENDS);
            $condition[] = Yii::$app->user->getId();
        }
        return $this->find()
            ->select(['post.user_id', 'post.id', 'post.description', 'post.created_at','post.filename', 'user.username', 'user.picture'])
            ->from('post')
            ->where(['user_id' => $condition])
            ->innerJoin('user', 'post.user_id = user.id')
            ->orderBy('created_at DESC')
            ->offset($start_page)
            ->limit($this->limit)
            ->asArray()
            ->all();
    }

    /**
     * Получить один пост
     * @param int $id
     * @return ActiveRecord|null
     */
    public function getPost(int $id): ?ActiveRecord
    {
        return $this->findOne($id);
    }

    /**
     * Лайк поста. При каждом дейстии пересчет топ-листа и лимита лайков
     * @param int $user_id
     * @param int $post_id
     * @return string|null вернет null в том числе, если пользователь уже лайкнул данный пост (механизм множеств, нельзя
     * добавить несколько одинаковых членов в множество)
     */
    public function changeStatus(int $user_id, int $post_id): ?string
    {
        $redis = Yii::$app->redis;
        if(self::isChangedByUser($user_id, $post_id)) {
            $increment = -1;
            $method = 'srem';
            if(Yii::$app->redis->hget('likes_day_limit', $user_id.'_day_limit_'.date('yy-m-d')) > 0)
            $this->changeLikesLimit($user_id, $increment);
        } else {
            $increment = 1;
            $method = 'sadd';
            if(!$this->isLikesLimitExceeded($user_id)) {
                $this->changeLikesLimit($user_id, $increment);
            } else {
                return 'exceeded';
            }
        }
        if ($redis->$method("user:{$user_id}:likes", $post_id) && $redis->$method("post:{$post_id}:likes", $user_id)) {
            $this->changeTopPost($post_id, $increment);
            return $method;
        } else return null;
    }


    public function changeLikesLimit(int $user_id, int $increment)
    {
        return Yii::$app->redis->hincrby('likes_day_limit', $user_id.'_day_limit_'.date('yy-m-d'), $increment);
    }

    /**
     * Проверяет, превышен ли лимит по лайкам за день
     * @param int $user_id
     * @return bool
     */
    public function isLikesLimitExceeded(int $user_id) :bool
    {
        return ($this->likesLimit <= Yii::$app->redis->hget('likes_day_limit', $user_id.'_day_limit_'.date('yy-m-d')));
    }

    /**
     * Меняет топ-лист, при лайке\анлайке поста
     * @param int $post_id id поста
     * @param int $topPostIncrement
     * @return void
     */
    private function changeTopPost(int $post_id, int $topPostIncrement) : void
    {
        Yii::$app->redis->zincrby('topz', $topPostIncrement, $post_id);
    }


    /**
     * Считает кол-во лайков у поста.
     * @param int $post_id
     * @return int
     */
    public static function countLikes(int $post_id): int
    {
        return intval(Yii::$app->redis->scard("post:{$post_id}:likes"));
    }

    /**
     * Проверка, ставил ли лайк пользователь за данный пост.
     * @param int $user_id
     * @param int $post_id
     * @return bool
     */
    public static function isChangedByUser(int $user_id, int $post_id): bool
    {
        return (bool)Yii::$app->redis->sismember("post:{$post_id}:likes", $user_id);
    }

    /**
     * Возвращает массив с постами, отсортированный по количеству лайков.
     * @param int|null $top количество постов для топ-листа
     * @return array|null массив с постами
     */
    public function getTopPosts(int $top = null): ?array
    {
        $top = $top ?? $this->top;
        $redis = Yii::$app->redis;
        $tops = $redis->zrevrangebyscore('topz', '+inf', '-inf', 'limit', 0, $top + 1);
        $posts = $this->find()
            ->select(['post.user_id', 'post.id', 'post.description', 'post.created_at','post.filename', 'user.username', 'user.picture'])
            ->from('post')
            ->where(['post.id' => $tops])
            ->innerJoin('user', 'post.user_id = user.id')
            ->limit($top)
            ->asArray()
            ->all();
        $final = [];
        foreach ($tops as $top) {
            foreach ($posts as $post) {
                if($top==$post['id']) {
                    $final[] = $post;
                }
            }
        }
        return $final;
    }
}
