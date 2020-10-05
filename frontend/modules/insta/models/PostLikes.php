<?php
declare(strict_types=1);

namespace frontend\modules\insta\models;

use yii\base\Model;
use frontend\modules\insta\models\Post;
use Yii;

/**
 * Class PostLikes отвечает за лайки постов. Данные хранятся в Redis.
 * @package frontend\modules\insta\models
 */
class PostLikes extends Model
{
    /** Статус, если кол-во лайков в день превышено*/
    const STATUS_EXCEEDED = 'exceeded';

    /**
     * @type int кол-во лайков в день
     */
    private $likesLimit = 10;

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
            if($redis->hget('likes_day_limit', $user_id.'_day_limit_'.date('yy-m-d')) > 0)
                $this->changeLikesLimit($user_id, $increment);
        } else {
            $increment = 1;
            $method = 'sadd';
            if(!$this->isLikesLimitExceeded($user_id)) {
                $this->changeLikesLimit($user_id, $increment);
            } else {
                return self::STATUS_EXCEEDED;
            }
        }
        if ($redis->$method("user:{$user_id}:likes", $post_id) && $redis->$method("post:{$post_id}:likes", $user_id)) {
            $this->changeTopPost($post_id, $increment);
            return $method;
        } else return null;
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
     * Изменяет лимитов лайков пользователя
     * @param int $user_id
     * @param int $increment в зависимости от добавления\удаления лайка переменная принимат 1 или -1
     * @return int количество лайков после изменения
     */
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
}
